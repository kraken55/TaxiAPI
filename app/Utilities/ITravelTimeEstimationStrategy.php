<?php

namespace App\Utilities;

interface ITravelTimeEstimationStrategy
{
    public static function estimateTravelTime(TravelTimeEstimationContext $context): float;
}

