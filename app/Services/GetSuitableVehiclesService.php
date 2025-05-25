<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Utilities\ITravelFareCalculationStrategy;
use App\Utilities\ITravelTimeEstimationStrategy;
use App\Utilities\MildHybridRangeCalculationStrategy;
use App\Utilities\StandardRangeCalculationStrategy;
use App\Utilities\TravelTimeEstimationContext;
use App\Utilities\TravelFareCalculationContext;
use Illuminate\Database\Eloquent\Collection;

class GetSuitableVehiclesService
{
    private ITravelTimeEstimationStrategy $travelTimeEstimation;
    private ITravelFareCalculationStrategy $travelFareCalculationStrategy;

    public function __construct(
        ITravelTimeEstimationStrategy $travelTimeEstimation,
        ITravelFareCalculationStrategy $travelFareCalculationStrategy
    ) {
        $this->travelTimeEstimation = $travelTimeEstimation;
        $this->travelFareCalculationStrategy = $travelFareCalculationStrategy;
    }

    public function findSuitableVehicles(int $passengerCount, int $distance): Collection
    {
        $requiredRangeForMildHybrid = MildHybridRangeCalculationStrategy::calculateRange($distance);
        $requiredRangeForOtherTypes = StandardRangeCalculationStrategy::calculateRange($distance);

        $suitableVehicles = Vehicle::join('fuel_types', 'vehicles.fuel_type_id', '=', 'fuel_types.id')
            ->where('vehicles.passenger_capacity', '>=', $passengerCount)
            ->where(function ($query) use ($requiredRangeForMildHybrid, $requiredRangeForOtherTypes) {
                $query->where(function ($q) use ($requiredRangeForMildHybrid) {
                    $q->where('fuel_types.name', 'Mild Hybrid')
                        ->where('vehicles.range', '>=', $requiredRangeForMildHybrid);
                })->orWhere(function ($q) use ($requiredRangeForOtherTypes) {
                    $q->where('fuel_types.name', '!=', 'Mild Hybrid')
                        ->where('vehicles.range', '>=', $requiredRangeForOtherTypes);
                });
            })
            ->selectRaw('vehicles.id,
                                     vehicles.passenger_capacity,
                                     vehicles.range,
                                     fuel_types.name as fuel_type,
                                     fuel_types.price_per_kilometer * ? as refueling_cost',
                        [$distance]
                
            )
            ->get();


        $travelTimeEstimationContext = new TravelTimeEstimationContext($distance);
        $travelTimeEstimate = $this->travelTimeEstimation->estimateTravelTime($travelTimeEstimationContext);

        $suitableVehicles->transform(function (Vehicle $vehicle) use ($distance, $passengerCount, $travelTimeEstimate) {
            $travelFareCalculationContext = new TravelFareCalculationContext($passengerCount, $travelTimeEstimate, $distance);
            $travelFare = $this->travelFareCalculationStrategy->calculateFare($travelFareCalculationContext);

            $vehicle->profit = $travelFare - $vehicle->refueling_cost;

            $vehicle->effective_range = $this->calculateEffectiveRange($vehicle->range, $vehicle->fuel_type);

            return $vehicle;
        });

        return $suitableVehicles->sortByDesc('profit')->values();
    }

    // Calculates the effective range of a vehicle based on its fuel type
    // Mild hybrid vehicles lose 1 km of range for every 2 km of travel in the first 50 km of the journey
    // and 1 km of range for every km of travel after that
    private const MILD_HYBRID_EFFECTIVE_RANGE_IMPROVEMENT_THRESHOLD = 50;
    private const MILD_HYBRID_EFFECTIVE_RANGE_IMPROVEMENT_VALUE = 0.5;

    private function calculateEffectiveRange(float $theoreticalRange, string $fuelTypeName): float
    {
        if ($fuelTypeName === 'Mild Hybrid')
        {
            return $theoreticalRange + (self::MILD_HYBRID_EFFECTIVE_RANGE_IMPROVEMENT_THRESHOLD * self::MILD_HYBRID_EFFECTIVE_RANGE_IMPROVEMENT_VALUE);
        }
        else
        {
            return $theoreticalRange;
        }
    }
}