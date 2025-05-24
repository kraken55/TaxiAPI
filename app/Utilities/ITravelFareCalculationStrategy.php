<?php

namespace App\Utilities;

interface ITravelFareCalculationStrategy
{
    public static function calculateFare(TravelFareCalculationContext $context): float;
}