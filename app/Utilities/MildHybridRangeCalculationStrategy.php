<?php

namespace App\Utilities;

class MildHybridRangeCalculationStrategy implements IRangeCalculationStrategy
{
    public static function calculateRange(float $distance): float
    {
        if ($distance <= 50) 
        {
            return $distance * 0.5;
        }
        else
        {
            return (50 * 0.5) + (($distance - 50) * 1);
        }
    }
}
