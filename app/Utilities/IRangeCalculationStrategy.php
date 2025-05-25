<?php

namespace App\Utilities;

interface IRangeCalculationStrategy
{
    public static function calculateRange(float $distance): float;
}

