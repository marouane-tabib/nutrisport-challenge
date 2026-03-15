<?php

namespace App\Http\Controllers\Api\Client;

use App\Exceptions\CartException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AddToCartRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Exception;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Display the cart contents with all items and their details.
     *
     * @param string $cartId The unique identifier of the cart
     * @return JsonResponse The response containing the cart data with items
     */
    public function show(string $cartId): JsonResponse
    {
        try {
            $cart = $this->cartService->show($cartId);
            $cart['cart_id'] = $cartId;
            $cart = new CartResource($cart);

            return successResponse($cart, code: 200);
        } catch (CartException $e) {
            return errorResponse($e->getMessage(), code: 404);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), code: 500);
        }
    }

    /**
     * Add a product item to the cart with quantity.
     *
     * @param AddToCartRequest $request The request containing product ID and quantity
     * @return JsonResponse The response containing the updated cart data
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        try {
            $cart = $this->cartService->store($request->site->id, $request->validated());
            $cart = new CartResource($cart);

            return successResponse($cart, code: 200);
        } catch (CartException $e) {
            return errorResponse($e->getMessage(), code: 400);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), code: 500);
        }
    }

    /**
     * Remove a specific product item from the cart.
     *
     * @param string $cartId The unique identifier of the cart
     * @param int $productId The ID of the product to remove
     * @return JsonResponse The response confirming the item removal
     */
    public function delete(string $cartId, int $productId): JsonResponse
    {
        try {
            $this->cartService->delete($cartId, $productId);

            return successResponse(message: "Product deleted from cart successfully", code: 200);
        } catch (CartException $e) {
            return errorResponse($e->getMessage(), code: 404);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), code: 500);
        }
    }

    /**
     * Clear the entire cart by removing all items.
     *
     * @param string $cartId The unique identifier of the cart
     * @return JsonResponse The response confirming the cart was cleared
     */
    public function clear(string $cartId): JsonResponse
    {
        try {
            $this->cartService->clear($cartId);

            return successResponse(message: "Cart cleared successfully", code: 200);
        } catch (CartException $e) {
            return errorResponse($e->getMessage(), code: 404);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), code: 500);
        }
    }
}