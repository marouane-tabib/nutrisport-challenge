<?php

namespace App\Http\Controllers\Api\Client;

use App\Exceptions\CartException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AddToCartRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Display the cart contents.
     *
     * @param string $cartId
     * @return \Illuminate\Http\JsonResponse
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
        }
    }

    /**
     * Add an item to the cart.
     *
     * @param AddToCartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        try {
            $cart = $this->cartService->store($request->site->id, $request->validated());
            $cart = new CartResource($cart);

            return successResponse($cart, code: 200);
        } catch (CartException $e) {
            return errorResponse($e->getMessage(), code: 400);
        }
    }

    /**
     * Remove an item from the cart.
     *
     * @param string $cartId
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(string $cartId, int $productId): JsonResponse
    {
        try {
            $this->cartService->delete($cartId, $productId);

            return successResponse(message: "Product deleted from cart successfully", code: 200);
        } catch (CartException $e) {
            return errorResponse($e->getMessage(), code: 404);
        }
    }

    /**
     * Clear the entire cart (delete all items).
     *
     * @param string $cartId
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(string $cartId): JsonResponse
    {
        try {
            $this->cartService->clear($cartId);

            return successResponse(message: "Cart cleared successfully", code: 200);
        } catch (CartException $e) {
            return errorResponse($e->getMessage(), code: 404);
        }
    }
}