<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = Order::first();
        $products = Product::take(3)->get();
        $prices = [29.99, 19.99, 29.99];

        foreach ($products as $index => $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $index + 1,
                'unit_price' => $prices[$index],
            ]);
        }
    }
}
