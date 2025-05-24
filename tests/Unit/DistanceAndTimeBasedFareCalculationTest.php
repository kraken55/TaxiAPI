<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Utilities\DistanceAndTimeBasedFareCalculation;
use App\Utilities\TravelFareCalculationContext;
class DistanceAndTimeBasedFareCalculationTest extends TestCase
{
    private DistanceAndTimeBasedFareCalculation $fareCalculation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fareCalculation = new DistanceAndTimeBasedFareCalculation();
    }

    public function test_calculateFare_with_valid_context()
    {
        $context = new TravelFareCalculationContext(1, 10, 100);
        $fare = $this->fareCalculation->calculateFare($context);
        $this->assertEquals( 202, $fare);
    }

    public function test_calculateFare_with_large_travel_time_and_multiple_passengers()
    {
        $context = new TravelFareCalculationContext(4, 1000, distance: 11);
        $fare = $this->fareCalculation->calculateFare($context);
        $this->assertEquals( 360, $fare);
    }

    // All types of travel fare calculation require a passenger count
    public function test_calculateFare_with_null_passengers()
    {
        $this->expectException(\InvalidArgumentException::class);

        $context = new TravelFareCalculationContext(0, 10, 100);
        $this->fareCalculation->calculateFare($context);
    }

    // The distance is required for this type of travel fare calculation
    public function test_calculateFare_with_null_distance()
    {
        $this->expectException(\InvalidArgumentException::class);

        $context = new TravelFareCalculationContext(1, travelTime: 10);
        $this->fareCalculation->calculateFare($context);
    }

    // The travel time is required for this type of travel fare calculation
    public function test_calculateFare_with_null_travel_time()
    {
        $this->expectException(\InvalidArgumentException::class);

        $context = new TravelFareCalculationContext(1, distance: 10);
        $this->fareCalculation->calculateFare($context);
    }

}
