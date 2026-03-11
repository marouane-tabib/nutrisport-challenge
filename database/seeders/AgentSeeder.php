<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Agent::create([
            'name' => 'Admin NutriSport',
            'email' => 'admin@nutrisport.com',
            'password' => 'password',
        ]);
    }
}
