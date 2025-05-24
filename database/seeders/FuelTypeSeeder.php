<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FuelType;

class FuelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FuelType::create(['name' => 'Gasoline', 'price_per_kilometer' => 2, 'efficiency_ratio' => 1]);
        FuelType::create(['name' => 'Mild Hybrid', 'price_per_kilometer' => 1.5, 'efficiency_ratio' => 0.5]);
        FuelType::create(['name' => 'Electric', 'price_per_kilometer' => 1, 'efficiency_ratio' => 1]);
    }
}
