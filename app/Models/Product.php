<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'stock'];

    /**
     * Get all prices for this product.
     *
     * @return HasMany
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Scope: Load products with their site-specific price.
     *
     * @param Builder $query
     * @param int $siteId
     * @return Builder
     */
    public function scopePriceForSite(Builder $query, int $siteId): Builder
    {
        return $query->with(['prices' => function ($query) use ($siteId) {
            $query->where('site_id', $siteId);
        }]);
    }

    /**
     * Get all order items for this product.
     *
     * @return HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the price from the loaded prices relationship.
     *
     * @return float|null
     */
    public function getPriceAttribute(): ?float
    {
        $priceModel = $this->prices->first();

        return $priceModel?->price;
    }

    /**
     * Check if product is in stock.
     *
     * @return bool
     */
    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Decrease product stock by quantity.
     *
     * @param int $quantity
     * @return void
     */
    public function decrementStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
    }
}
