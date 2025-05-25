<?php

namespace App\Utilities;

use App\Models\FuelType;

class TravelTimeEstimationContext
{
    public function __construct(
        private float $distance, // Distance is always required for travel time estimation
        private ?FuelType $fuelType = null,
    ) {
        if ($this->distance < 0)
        {
            throw new \InvalidArgumentException('Distance must not be negative!');
        }
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function getFuelType(): ?FuelType
    {
        return $this->fuelType;
    }

}