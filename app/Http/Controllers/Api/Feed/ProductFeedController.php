<?php

namespace App\Http\Controllers\Api\Feed;

use App\Exceptions\ProductException;
use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductFeedController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function show(string $format): Response|JsonResponse
    {
        try {
            $feed = $this->productService->generateFeed($format);

            return feedResponse($feed['content'], $feed['content_type']);
        } catch (ProductException $e) {
            return errorResponse($e->getMessage(), 404);
        }
    }
}
