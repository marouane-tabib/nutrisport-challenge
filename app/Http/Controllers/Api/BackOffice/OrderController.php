<?php

namespace App\Http\Controllers\Api\BackOffice;

use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\ListOrdersRequest;
use App\Http\Resources\OrderListResource;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}
    
    /**
     * List orders from the last 5 days.
     */
    public function index(ListOrdersRequest $request)
    {
        try {
            $orders = $this->orderService->getRecentOrders($request->validated());
            $orders = OrderListResource::collection($orders);

            return successResponse($orders);
        } catch (OrderException $e) {
            return errorResponse($e, 504);
        }
    }
}
