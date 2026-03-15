<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

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
    
    /**
     * Create a product and its prices for multiple sites.
     *
     * @param array $productData  ['name' => string, 'stock' => int]
     * @param array $prices       array of ['site_id' => int, 'price' => float]
     * @return \App\Models\Product
     * @throws InvalidArgumentException
     */
    public function store(array $data): Product
    {
        $product = DB::transaction(function () use ($data) {
            $product = Product::create($data);
            $product->prices()->createMany($data['prices']);

            return $product;
        });

        return $product;
    }
}
