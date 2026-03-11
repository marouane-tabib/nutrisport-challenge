<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frSite = Site::where('domain', 'nutri-sport.fr')->first();

        // Create a test user if none exists
        $user = User::firstOrCreate(
            ['email' => 'jean@example.com', 'site_id' => $frSite->id],
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'password' => 'password',
                'site_id' => $frSite->id,
            ]
        );

        Order::create([
            'user_id' => $user->id,
            'site_id' => $frSite->id,
            'total' => 79.97,
            'status' => OrderStatus::PENDING,
            'payment_method' => PaymentMethod::BANK_TRANSFER,
            'shipping_full_name' => 'Jean Dupont',
            'shipping_address' => '12 Rue de la Paix',
            'shipping_city' => 'Paris',
            'shipping_country' => 'France',
            'paid_amount' => 0,
        ]);
    }
}
