<?php

namespace App\Utilities;

interface ITravelTimeEstimationStrategy
{
    public function estimateTravelTime(TravelTimeEstimationContext $context): float;
}

