<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'NutriSport ' . fake()->country(),
            'domain' => 'nutri-sport.' . fake()->unique()->tld(),
            'country_code' => fake()->countryCode(),
            'is_active' => true,
        ];
    }
}
