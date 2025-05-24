<?php

namespace App\Utilities; 

class DistanceOnlyTravelTimeEstimation implements ITravelTimeEstimationStrategy
{
    private const MIN_PER_KM_UNDER_50_KM = 2;
    private const MIN_PER_KM_OVER_50_KM = 1;

    public function estimateTravelTime(TravelTimeEstimationContext $context): float
    {
        $distance = $context->getDistance();

        if ($distance < 50)
        {
            return $distance * self::MIN_PER_KM_UNDER_50_KM;
        }
        else
        {
            return 50 * self::MIN_PER_KM_UNDER_50_KM + ($distance - 50) * self::MIN_PER_KM_OVER_50_KM;
        }
    }
}
