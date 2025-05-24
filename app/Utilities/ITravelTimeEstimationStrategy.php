<?php

namespace App\Utilities;

use App\Models\Vehicle;

class TravelTimeEstimationContext
{
    public function __construct(
        private float $distance, // Distance is always required for travel time estimation
        private ?Vehicle $vehicle = null,
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

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

}

interface ITravelTimeEstimationStrategy
{
    public function estimateTravelTime(TravelTimeEstimationContext $context): float;
}

