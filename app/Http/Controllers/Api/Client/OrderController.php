<?php

namespace App\Http\Controllers\Api\Client;

use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Get order history for authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = auth('client')->user();
            $perPage = $request->input('per_page', 15);

            $orders = $this->orderService->index($user->id, $perPage);
            $orders = OrderResource::collection($orders);

            return successResponse($orders);
        } catch (OrderException $e) {
            return errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Place an order from the cart.
     *
     * @param PlaceOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PlaceOrderRequest $request)
    {
        try {
            $order = $this->orderService->store($request->site->id, $request->validated());
            $order = new OrderResource($order);

            return successResponse($order);
        } catch (OrderException $e) {
            return errorResponse($e->getMessage(), 400);
        }
    }
}