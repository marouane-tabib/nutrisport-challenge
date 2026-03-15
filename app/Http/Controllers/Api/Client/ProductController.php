<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ListProductsRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
    ) {}

    /**
     * Get paginated list of products with prices for the current site.
     *
     * @param ListProductsRequest $request
     * @return JsonResponse
     */
    public function index(ListProductsRequest $request): JsonResponse
    {
        try {
            $products = $this->productService->index($request->site->id, $request->validated());
            $formattedProducts = formatPaginatedResource($products, ProductResource::class);

            return successResponse($formattedProducts);
        } catch (ModelNotFoundException $e) {
            return errorResponse($e, 404);
        }
    }

    /**
     * Get a single product with its price for the current site.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->show($request->site->id, $id);
            $product = new ProductResource($product);

            return successResponse($product);
        } catch (ModelNotFoundException $e) {
            return errorResponse($e, 404);
        }
    }
}
