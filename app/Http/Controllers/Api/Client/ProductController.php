<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ListProductsRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Exception;
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
     * Get paginated list of products with site-specific pricing.
     *
     * @param ListProductsRequest $request The request containing pagination and filter parameters
     * @return JsonResponse The response containing paginated products with pricing
     */
    public function index(ListProductsRequest $request): JsonResponse
    {
        try {
            $products = $this->productService->index($request->site->id, $request->validated());
            $formattedProducts = formatPaginatedResource($products, ProductResource::class);

            return successResponse($formattedProducts);
        } catch (ModelNotFoundException $e) {
            return errorResponse($e, 404);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get a single product with its site-specific pricing and details.
     *
     * @param Request $request The request containing site information
     * @param int $id The product ID to retrieve
     * @return JsonResponse The response containing the product data with pricing
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->show($request->site->id, $id);
            $product = new ProductResource($product);

            return successResponse($product);
        } catch (ModelNotFoundException $e) {
            return errorResponse($e, 404);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }
}
