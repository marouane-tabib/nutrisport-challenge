<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Whey Protein 1kg', 'stock' => 50],
            ['name' => 'BCAA Capsules 120', 'stock' => 80],
            ['name' => 'Creatine Monohydrate', 'stock' => 60],
            ['name' => 'Pre-Workout Energy', 'stock' => 40],
            ['name' => 'Mass Gainer 3kg', 'stock' => 25],
            ['name' => 'Omega 3 Fish Oil', 'stock' => 100],
            ['name' => 'Multivitamin Complex', 'stock' => 90],
            ['name' => 'Protein Bar Box (12)', 'stock' => 0],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
