<?php

namespace App\Utilities;

class DistanceAndTimeBasedFareCalculation implements ITravelFareCalculationStrategy
{
    private const PRICE_PER_KM = 2;
    private const PRICE_PER_HALF_HOUR = 2; // 2 euros for every half hour started

    public function calculateFare(TravelFareCalculationContext $context): float
    {
        $passengers = $context->getPassengers();

        $distance = $context->getDistance();
        $travelTime = $context->getTravelTime();

        if ($distance === null || $travelTime === null)
        {
            throw new \InvalidArgumentException('Distance and travel time are required for this type of fare calculation!');
        }

        $distanceFare = $distance * self::PRICE_PER_KM;
        $timeFare = ceil($travelTime / 30) * self::PRICE_PER_HALF_HOUR;

        return $passengers * ($distanceFare + $timeFare);
    }
}
