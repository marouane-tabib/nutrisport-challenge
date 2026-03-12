<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService extends BaseService
{
    /**
     * Get paginated products with prices for a specific site.
     *
     * @param int $siteId
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function index(int $siteId, array $data): LengthAwarePaginator
    {
        $products = Product::select('id', 'name', 'stock')
            ->priceForSite($siteId)
            ->paginate(perPage: $data['perPage'] ?? 10, page: $data['page'] ?? 1);

        return $products;
    }

    /**
     * Find a single product with its price for a specific site.
     *
     * @param int $siteId
     * @param int $productId
     * @return Product
     */
    public function show(int $siteId, int $productId): Product
    {
        $product = Product::select('id', 'name', 'stock')
            ->priceForSite($siteId)
            ->findOrFail($productId);
            
        return $product;
    }
}
