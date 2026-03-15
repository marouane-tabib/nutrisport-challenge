<?php

namespace App\Http\Controllers\Api\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\CreateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Exception;
use InvalidArgumentException;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Create a new product with per-site pricing configuration.
     *
     * @param CreateProductRequest $request The request containing product data and pricing per site
     * @return \Illuminate\Http\JsonResponse The response containing the created product resource
     */
    public function store(CreateProductRequest $request)
    {
        try {
            $products = $this->productService->store($request->validated());
            $products = new ProductResource($products);

            return successResponse($products);
        } catch (InvalidArgumentException $e) {
            return errorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }
}
