<?php

namespace App\Http\Controllers\Api\Feed;

use App\Exceptions\ProductException;
use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductFeedController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Generate and return a product feed in the specified format (RSS, Atom, etc).
     *
     * @param string $format The feed format (e.g., 'rss', 'atom')
     * @return Response|JsonResponse The feed content as Response or error as JsonResponse
     */
    public function show(string $format): Response|JsonResponse
    {
        try {
            $feed = $this->productService->generateFeed($format);

            return feedResponse($feed['content'], $feed['content_type']);
        } catch (ProductException $e) {
            return errorResponse($e->getMessage(), 404);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }
}
