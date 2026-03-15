<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use InvalidArgumentException;

class OrderService extends BaseService
{
    /**
     * Constructor.
     */
    public function __construct(
        protected CartService $cartService
    ) {}
    
    /**
     * Place an order from a cart.
     *
     * @param int $siteId
     * @param array $data  // cart_id, shipping_full_name, shipping_address, shipping_city, shipping_country, payment_method
     * @return Order
     *
     * @throws RuntimeException If any product is out of stock
     */
    public function store(int $siteId, array $data): Order
    {
        $user = auth('client')->user();
        
        // Retrieve cart from cache
        $cart = $this->cartService->findCart($data['cart_id']);
        
        if (empty($cart['items'])) {
            throw OrderException::emptyCart();
        }
        
        $cartItems = $cart['items'];

        $this->validateStock($cartItems);

        // 2. Use a database transaction
        $order = DB::transaction(function () use ($user, $siteId, $cartItems, $data) {
            // 3. Create the order
            $order = $this->createOrder($user, $siteId, $cartItems, $data);

            // 4. Create order items and decrement stock
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);

                // Decrement product stock using relationship
                Product::findOrFail($item['product_id'])
                        ->decrement('stock', $item['quantity']);
            }

            // 5. Fire event (will trigger emails and broadcast)
            event(new OrderPlaced($order));

            return $order;
        });
        
        // 3. Clear the cart after successful order
        $this->cartService->clear($data['cart_id']);
        
        return $order;
    }

    /**
     * Get paginated orders for a specific user.
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(int $userId, int $perPage = 15)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $orders;
    }

    /**
     * Get paginated orders for a specific user.
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getRecentOrders($data)
    {
        $orders = Order::with(['user', 'site'])
            ->where('created_at', '>=', now()->subDays($data['days'])->startOfDay())
            ->orderBy('created_at', 'desc')
            ->paginate($data['perPage'] ?? 1, page: $data['page'] ?? 10);

        return $orders;
    }

    /**
     * Validate that all products in the cart have sufficient stock.
     *
     * @param array $cartItems
     * @throws RuntimeException
     */
    protected function validateStock(array $cartItems): void
    {
        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                throw OrderException::productNotFound($item['product_id']);
            }
            
            if ($product->stock < $item['quantity']) {
                throw OrderException::insufficientStock($product->name, $item['product_id']);
            }
        }
    }

    /**
     * Calculate total from cart items.
     *
     * @param array $cartItems
     * @return float
     */
    protected function calculateTotal(array $cartItems): float
    {
        return array_sum(array_column($cartItems, 'line_total'));
    }

    protected function createOrder($user, $siteId, $cartItems, $data): Order
    {
        return Order::create([
            'user_id'               => $user->id,
            'site_id'               => $siteId,
            'total'                 => $this->calculateTotal($cartItems),
            'status'                => OrderStatus::PENDING,
            'payment_method'        => $data['payment_method'],
            'shipping_full_name'    => $data['shipping_full_name'],
            'shipping_address'      => $data['shipping_address'],
            'shipping_city'         => $data['shipping_city'],
            'shipping_country'      => $data['shipping_country'],
            'paid_amount'           => 0,
        ]);
    }
}