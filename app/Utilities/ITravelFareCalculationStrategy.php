<?php

namespace App\Utilities;

class TravelFareCalculationContext
{
    public function __construct(
        private int $passengers, // Passenger count is always required for fare calculation
        private ?float $travelTime = null,
        private ?float $distance = null,
    ) {
        if ($this->passengers <= 0)
        {
            throw new \InvalidArgumentException('Passenger count must be greater than 0!');
        }

        if ( $this->travelTime !== null && $this->travelTime < 0)
        {
            throw new \InvalidArgumentException('Travel time must be greater than 0!');
        }

        if ($this->distance !== null && $this->distance < 0)
        {
            throw new \InvalidArgumentException('Distance must not be negative!');
        }
    }

    public function getPassengers(): int
    {
        return $this->passengers;
    }

    public function getTravelTime(): ?float
    {
        return $this->travelTime;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }
}

interface ITravelFareCalculationStrategy
{
    public function calculateFare(TravelFareCalculationContext $context): float;
}