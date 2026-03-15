<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use App\Services\Feed\JsonFeedGenerator;
use App\Services\Feed\XmlFeedGenerator;
use App\Exceptions\ProductException;

class ProductService extends BaseService
{
    /**
     * Get paginated products with site-specific prices.
     *
     * @param int $siteId The site ID to retrieve products for
     * @param array $data The pagination data (perPage, page)
     * @return LengthAwarePaginator The paginated products with pricing
     */
    public function index(int $siteId, array $data): LengthAwarePaginator
    {
        $products = Product::select('id', 'name', 'stock')
            ->priceForSite($siteId)
            ->paginate(perPage: $data['perPage'] ?? 10, page: $data['page'] ?? 1);

        return $products;
    }

    /**
     * Find a single product with its site-specific price.
     *
     * @param int $siteId The site ID to retrieve the product for
     * @param int $productId The product ID to retrieve
     * @return Product The product with pricing information
     */
    public function show(int $siteId, int $productId): Product
    {
        $product = Product::select('id', 'name', 'stock')
            ->priceForSite($siteId)
            ->findOrFail($productId);
            
        return $product;
    }
    
    /**
     * Create a new product with per-site pricing configuration.
     *
     * @param array $data The product data containing name, stock, and prices array
     * @return Product The created product model
     * @throws InvalidArgumentException When data validation fails
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

    /**
     * Generate a product feed in the specified format (JSON or XML).
     *
     * @param string $format The feed format ('json' or 'xml')
     * @return array The feed content and content type
     * @throws ProductException When the feed format is not supported
     */
    public function generateFeed(string $format): ?array
    {
        $generators = [
            'json' => JsonFeedGenerator::class,
            'xml'  => XmlFeedGenerator::class,
        ];

        if (!array_key_exists($format, $generators)) {
            throw ProductException::unsupportedFeedFormat($format);
        }

        $products = Product::where('stock', '>', 0)
            ->orderBy('id')
            ->select('id', 'name', 'stock')
            ->get()
            ->toArray();

        $generator = (new $generators[$format]);

        return [
            'content'      => $generator->generate($products),
            'content_type' => $generator->contentType(),
        ];
    }
}
