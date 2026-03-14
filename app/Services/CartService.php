<?php

namespace App\Services;

use App\Exceptions\CartException;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use InvalidArgumentException;

class CartService extends BaseService
{
    /**
     * Cache expiration time (seconds).
     */
    protected $cartExpiration;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->cartExpiration = Carbon::now()->addDays(3);
    }

    public function show(string $cartId)
    {
        return $this->findCart($cartId);
    }

    /**
     * Add an item to the cart.
     *
     * @param array{ cartId: string|null, productId: int, quantity: int, siteId: int } $data
     * @return array{ cart_id: string, items: array, total: float }
     *
     * @throws InvalidArgumentException
     * @throws CartException
     */
    public function store(int $siteId, array $data): array
    {
        $product = Product::select('id', 'name')
                ->priceForSite($siteId)
                ->findOrFail($data['product_id']);

        // Get current cart
        $cart = $this->findCart($data['cart_id']);
        $cartItems = $cart['items'];

        // Find existing item index
        $productIndex = $this->findProductIndex($cartItems, $data['product_id']);

        if ($productIndex !== null) {
            $cartItems = $this->updateExitItems($cartItems, $productIndex, $data['quantity'], $product->price);
        } else {
            // Add new item
            $cartItems[] = [
                'product_id'   => $data['product_id'],
                'product_name' => $product->name,
                'quantity'     => $data['quantity'],
                'unit_price'   => $product->price,
                'line_total'   => $product->price * $data['quantity'],
            ];
        }

        // Update cart items and recalculate total
        $cart['items'] = $cartItems;
        $cart['total'] = $this->recalculateTotal($cart['items']);

        // Store in cache
        Cache::put($this->cartKey($data['cart_id']), $cart, $this->cartExpiration);

        return [
            'cart_id' => $data['cart_id'],
            'items'   => $cart['items'],
            'total'   => $cart['total'],
        ];
    }

    /**
     * Remove an item from the cart.
     *
     * @param string $cartId
     * @param int $productId
     * @return array{items: array, total: float}
     *
     * @throws CartException
     */
    public function delete(string $cartId, int $productId): void
    {
        if (!$this->cartExists($cartId)) {
            throw CartException::cartNotFound($cartId);
        }

        $cart = $this->findCart($cartId);

        // Filter out the item
        $cart['items'] = array_values(array_filter(
            $cart['items'],
            fn($item) => $item['product_id'] !== $productId
        ));

        // Recalculate total
        $cart['total'] = $this->recalculateTotal($cart['items']);

        Cache::put($this->cartKey($cartId), $cart, $this->cartExpiration);
    }

    /**
     * Clear the cart (remove from cache).
     *
     * @param string $cartId
     * @return void
     */
    public function clear(string $cartId): void
    {
        if (!$this->cartExists($cartId)) {
            throw CartException::cartNotFound($cartId);
        }
        
        Cache::forget($this->cartKey($cartId));
    }

    
    /**
     * Generate the cache key for a cart.
     */
    protected function cartKey(string $cartId): string
    {
        return "cart#{$cartId}";
    }
    
    /**
     * Retrieve a cart from cache. If not found, return empty cart.
     *
     * @param string $cartId
     * @return array{items: array, total: float}
     */
    public function findCart(string $cartId): array
    {
        return Cache::get($this->cartKey($cartId), [
            'items' => [],
            'total' => 0.0,
        ]);
    }

    /**
     * Check if a cart exists.
     *
     * @param string $cartId
     * @return bool
     */
    protected function cartExists(string $cartId): bool
    {
        return Cache::has($this->cartKey($cartId));
    }

    /**
     * Recalculate the total from items.
     *
     * @param array $items
     * @return float
     */
    protected function recalculateTotal(array $items): float
    {
        return (float) array_sum(array_column($items, 'line_total'));
    }

    /**
     * Find the index of a product in cart items.
     *
     * @param array $items
     * @param int $productId
     * @return int|null
     */
    protected function findProductIndex(array $items, int $productId): ?int
    {
        foreach ($items as $index => $item) {
            if ($item['product_id'] === $productId) {
                return $index;
            }
        }
        return null;
    }

    protected function updateExitItems(array $items, int $itemIndex, int $quantityToAdd, int|float $productPrice): array
    {
        // increment quantity
        $items[$itemIndex]['quantity'] += $quantityToAdd;

        // update line total
        $items[$itemIndex]['line_total'] = $items[$itemIndex]['quantity'] * $productPrice;

        return $items;
    }
}