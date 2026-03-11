<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Site;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = Site::all();
        $products = Product::all();
        $basePrices = [29.99, 19.99, 24.99, 34.99, 49.99, 14.99, 12.99, 29.99];

        foreach ($products as $index => $product) {
            foreach ($sites as $siteIndex => $site) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'site_id' => $site->id,
                    'price' => $basePrices[$index] + ($siteIndex * 1.00),
                ]);
            }
        }
    }
}
