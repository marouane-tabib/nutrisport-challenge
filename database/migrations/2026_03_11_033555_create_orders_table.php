<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('site_id')->constrained('sites');
            $table->decimal('total', 10, 2);
            $table->string('status')->default(OrderStatus::PENDING->value);
            $table->string('payment_method')->default(PaymentMethod::BANK_TRANSFER->value);
            $table->string('shipping_full_name');
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_country');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->timestamps();
            $table->index('user_id');
            $table->index('site_id');
            $table->index('created_at');
            $table->index(['site_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
