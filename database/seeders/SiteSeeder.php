<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = [
            ['name' => 'NutriSport France', 'domain' => 'nutri-sport.fr', 'country_code' => 'FR'],
            ['name' => 'NutriSport Italie', 'domain' => 'nutri-sport.it', 'country_code' => 'IT'],
            ['name' => 'NutriSport Belgique', 'domain' => 'nutri-sport.be', 'country_code' => 'BE'],
        ];

        foreach ($sites as $site) {
            Site::create($site);
        }
    }
}
