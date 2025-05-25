<?php

namespace App\Utilities;

interface ITravelFareCalculationStrategy
{
    public function calculateFare(TravelFareCalculationContext $context): float;
}