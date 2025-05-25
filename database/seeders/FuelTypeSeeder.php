<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FuelType;
use App\Enums\FuelTypeEnum;
class FuelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FuelType::create(['name' => FuelTypeEnum::GASOLINE, 'price_per_kilometer' => 2]);
        FuelType::create(['name' => FuelTypeEnum::MILD_HYBRID, 'price_per_kilometer' => 1.5]);
        FuelType::create(['name' => FuelTypeEnum::ELECTRIC, 'price_per_kilometer' => 1]);
    }
}
