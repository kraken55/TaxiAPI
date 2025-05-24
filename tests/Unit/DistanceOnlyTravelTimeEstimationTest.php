<?php

namespace Tests\Unit;

use App\Utilities\DistanceOnlyTravelTimeEstimation;
use App\Utilities\TravelTimeEstimationContext;
use PHPUnit\Framework\TestCase;
class DistanceOnlyTravelTimeEstimationTest extends TestCase
{
    private DistanceOnlyTravelTimeEstimation $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->strategy = new DistanceOnlyTravelTimeEstimation();
    }

    public function test_distance_only_travel_time_estimation_returns_correct_value(): void
    {
        $context = new TravelTimeEstimationContext(100);

        $result = $this->strategy->estimateTravelTime($context);

        $this->assertEquals(150, $result);
    }

    public function test_distance_only_travel_time_estimation_with_0_distance_returns_0(): void
    {
        $context = new TravelTimeEstimationContext(0);

        $result = $this->strategy->estimateTravelTime($context);

        $this->assertEquals(0, $result);
    }

    public function test_distance_only_travel_time_estimation_with_negative_distance_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $context = new TravelTimeEstimationContext(-1);

        $this->strategy->estimateTravelTime($context);
    }

    public function test_distance_only_travel_time_estimation_with_distance_under_50_km_returns_correct_value(): void
    {
        $context = new TravelTimeEstimationContext(49);

        $result = $this->strategy->estimateTravelTime($context);

        $this->assertEquals(98, $result);
    }
}
