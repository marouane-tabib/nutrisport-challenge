<?php

namespace App\Http\Controllers\Api\Client;

use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Get paginated order history for the authenticated user.
     *
     * @param Request $request The request containing pagination parameters (per_page)
     * @return \Illuminate\Http\JsonResponse The response containing the user's orders
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
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Place a new order from the authenticated user's cart.
     *
     * @param PlaceOrderRequest $request The request containing cart ID, shipping, and payment method
     * @return \Illuminate\Http\JsonResponse The response containing the created order data
     */
    public function store(PlaceOrderRequest $request)
    {
        try {
            $order = $this->orderService->store($request->site->id, $request->validated());
            $order = new OrderResource($order);

            return successResponse($order);
        } catch (OrderException $e) {
            return errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }
}