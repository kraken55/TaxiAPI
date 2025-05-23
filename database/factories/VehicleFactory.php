<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FuelType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'passenger_capacity' => $this->faker->numberBetween(2, 6),
            'range' => $this->faker->numberBetween(100, 1000),
            'fuel_type_id' => FuelType::inRandomOrder()->first()->id,
        ];
    }
}
