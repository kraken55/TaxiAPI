<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Vehicle;
use App\Models\FuelType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class VehiclesSuitableEndpointTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestData();
    }

    private function setUpTestData(): void
    {
        $this->seed(\Database\Seeders\FuelTypeSeeder::class);

        $gasoline = FuelType::where('name', 'Gasoline')->first();
        $electric = FuelType::where('name', 'Electric')->first();
        $mildHybrid = FuelType::where('name', 'Mild Hybrid')->first();

        // Create some extra vehicles for testing to make sure each fuel type is tested
        Vehicle::create([
            'passenger_capacity' => 2,
            'range' => 150,
            'fuel_type_id' => $gasoline->id
        ]);

        Vehicle::create([
            'passenger_capacity' => 4,
            'range' => 200,
            'fuel_type_id' => $electric->id
        ]);

        Vehicle::create([
            'passenger_capacity' => 6,
            'range' => 300,
            'fuel_type_id' => $mildHybrid->id
        ]);

        Vehicle::create([
            'passenger_capacity' => 4,
            'range' => 100,
            'fuel_type_id' => $gasoline->id
        ]);

        Vehicle::create([
            'passenger_capacity' => 8,
            'range' => 400,
            'fuel_type_id' => $electric->id
        ]);
    }

    public function test_can_get_suitable_vehicles_with_valid_parameters()
    {
        $response = $this->getJson('/api/vehicles/suitable?passengers=4&distance=100');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => [
                         'id',
                         'passenger_capacity',
                         'range',
                         'fuel_type',
                         'refueling_cost',
                         'profit',
                         'effective_range'
                     ]
                 ]);

        $vehicles = $response->json();
        
        // All returned vehicles should have passenger capacity >= requested
        foreach ($vehicles as $vehicle) {
            $this->assertGreaterThanOrEqual(4, $vehicle['passenger_capacity']);
            $this->assertArrayHasKey('profit', $vehicle);
            $this->assertArrayHasKey('effective_range', $vehicle);
            $this->assertArrayHasKey('refueling_cost', $vehicle);
        }
    }

    public function test_suitable_vehicles_are_sorted_by_profit_descending()
    {
        $response = $this->getJson('/api/vehicles/suitable?passengers=2&distance=50');

        $response->assertStatus(200);

        $vehicles = $response->json();
        
        // Check that vehicles are sorted by profit in descending order
        for ($i = 0; $i < count($vehicles) - 1; $i++) {
            $this->assertGreaterThanOrEqual(
                $vehicles[$i + 1]['profit'],
                $vehicles[$i]['profit'],
                'Vehicles should be sorted by profit in descending order'
            );
        }
    }

    public function test_calculates_refueling_cost_correctly()
    {
        $distance = 100;
        $response = $this->getJson("/api/vehicles/suitable?passengers=2&distance={$distance}");

        $response->assertStatus(200);

        $vehicles = $response->json();
        
        foreach ($vehicles as $vehicle) {
            // Get the fuel type price per kilometer from database
            $fuelType = FuelType::where('name', $vehicle['fuel_type'])->first();
            $expectedRefuelingCost = $fuelType->price_per_kilometer * $distance;
            
            $this->assertEquals(
                $expectedRefuelingCost,
                $vehicle['refueling_cost'],
                'Refueling cost should be calculated as fuel_price_per_km * distance'
            );
        }
    }

    public function test_mild_hybrid_vehicles_have_improved_effective_range()
    {
        $response = $this->getJson('/api/vehicles/suitable?passengers=2&distance=50');

        $response->assertStatus(200);

        $vehicles = $response->json();
        $mildHybridVehicles = array_filter($vehicles, function($vehicle) {
            return $vehicle['fuel_type'] === 'Mild Hybrid';
        });

        foreach ($mildHybridVehicles as $vehicle) {
            // Mild hybrid should have effective_range > theoretical range due to efficiency improvements
            $this->assertGreaterThan(
                $vehicle['range'],
                $vehicle['effective_range'],
                'Mild hybrid vehicles should have effective range greater than theoretical range'
            );
        }
    }

    public function test_validation_error_when_passengers_parameter_is_missing()
    {
        $response = $this->getJson('/api/vehicles/suitable?distance=100');

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['passengers']);
    }

    public function test_validation_error_when_distance_parameter_is_missing()
    {
        $response = $this->getJson('/api/vehicles/suitable?passengers=4');

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['distance']);
    }

    public function test_validation_error_when_passengers_is_not_integer()
    {
        $response = $this->getJson('/api/vehicles/suitable?passengers=invalid&distance=100');

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['passengers']);
    }

    public function test_validation_error_when_distance_is_not_numeric()
    {
        $response = $this->getJson('/api/vehicles/suitable?passengers=4&distance=invalid');

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['distance']);
    }

    public function test_validation_error_when_passengers_is_less_than_one()
    {
        $response = $this->getJson('/api/vehicles/suitable?passengers=0&distance=100');

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['passengers']);
    }

    public function test_validation_error_when_distance_is_negative()
    {
        $response = $this->getJson('/api/vehicles/suitable?passengers=4&distance=-10');

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['distance']);
    }

    public function test_returns_empty_array_when_no_vehicles_meet_criteria()
    {
        $response = $this->getJson('/api/vehicles/suitable?passengers=10&distance=100');

        $response->assertStatus(200)
                 ->assertJson([]);
    }

    public function test_handles_zero_distance()
    {
        $response = $this->getJson('/api/vehicles/suitable?passengers=2&distance=0');

        $response->assertStatus(200);

        $vehicles = $response->json();
        
        foreach ($vehicles as $vehicle) {
            $this->assertEquals(0, $vehicle['refueling_cost']);
        }
    }

    public function test_handles_decimal_distance()
    {
        $distance = 2.7;
        $response = $this->getJson("/api/vehicles/suitable?passengers=2&distance={$distance}");

        $response->assertStatus(200);

        $vehicles = $response->json();
        
        foreach ($vehicles as $vehicle) {
            $fuelType = FuelType::where('name', $vehicle['fuel_type'])->first();
            $expectedRefuelingCost = $fuelType->price_per_kilometer * $distance;
            
            $this->assertEquals(
                $expectedRefuelingCost,
                $vehicle['refueling_cost']
            );
        }
    }

    public function test_filters_vehicles_with_insufficient_range()
    {
        $gasoline = FuelType::where('name', 'Gasoline')->first();
        Vehicle::create([
            'passenger_capacity' => 2,
            'range' => 10, // Very low range
            'fuel_type_id' => $gasoline->id
        ]);

        $response = $this->getJson('/api/vehicles/suitable?passengers=2&distance=50');

        $response->assertStatus(200);

        $vehicles = $response->json();
        

        foreach ($vehicles as $vehicle) {
            $this->assertGreaterThan(10, $vehicle['range']);
        }
    }
} 