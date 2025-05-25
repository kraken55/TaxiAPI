<?php

namespace App\Utilities;

class StandardRangeCalculationStrategy implements IRangeCalculationStrategy
{
    public static function calculateRange(float $distance): float
    {
        return $distance;
    }
}
