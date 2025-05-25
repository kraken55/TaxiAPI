<?php

namespace App\Http\Controllers;

use App\Utilities\DistanceOnlyTravelTimeEstimation;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Utilities\TravelFareCalculationContext;
use App\Utilities\TravelTimeEstimationContext;
use App\Utilities\DistanceAndTimeBasedFareCalculation;
use App\Utilities\MildHybridRangeCalculationStrategy;
use App\Utilities\StandardRangeCalculationStrategy;
class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();
        return response()->json($vehicles);
    }

    public function show($id)
    {
        $vehicle = Vehicle::find($id);
        return response()->json($vehicle);
    }

    public function store(Request $request)
    {
        $vehicle = Vehicle::create($request->all());
        return response()->json($vehicle, 201);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        $vehicle->update($request->all());
        return response()->json($vehicle);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);
        $vehicle->delete();
        return response()->json(null, 204);
    }

    public function getSuitableVehicles(Request $request)
    {
        $request->validate([
            'passengers' => 'required|integer|min:1',
            'distance' => 'required|numeric|min:0',
        ]);

        $passengers = (int) $request->input('passengers');
        $distance = (float) $request->input('distance');

        // Calculate the required range for mild hybrid and other vehicles
        // Mild hybrid vehicles lose 1 km of range for every 2 km of travel in the first 50 km of the journey
        // and 1 km of range for every km of travel after that

        $requiredRangeForMildHybrid = MildHybridRangeCalculationStrategy::calculateRange($distance);
        $requiredRangeForOtherVehicles = StandardRangeCalculationStrategy::calculateRange($distance);

        $suitableVehicles = Vehicle::join('fuel_types', 'vehicles.fuel_type_id', '=', 'fuel_types.id')
            ->where('vehicles.passenger_capacity', '>=', $passengers)
            ->where(function ($query) use ($requiredRangeForMildHybrid, $requiredRangeForOtherVehicles) {
                $query->where(function ($q) use ($requiredRangeForMildHybrid) {
                    $q->where('fuel_types.name', 'Mild Hybrid')
                        ->where('vehicles.range', '>=', $requiredRangeForMildHybrid);
                })
                ->orWhere(function ($q) use ($requiredRangeForOtherVehicles) {
                    $q->where('fuel_types.name', '!=', 'Mild Hybrid')
                        ->where('vehicles.range', '>=', $requiredRangeForOtherVehicles);
                });
            })
            ->selectRaw('vehicles.id,
                         vehicles.passenger_capacity,
                         vehicles.range,
                         fuel_types.name as fuel_type,
                         fuel_types.price_per_kilometer * ? as refueling_cost', [$distance])
            ->get();

        $suitableVehicles->transform(function (Vehicle $vehicle) use ($distance, $passengers) {
            $travelTimeEstimationContext = new TravelTimeEstimationContext($distance);
            $travelTime = DistanceOnlyTravelTimeEstimation::estimateTravelTime($travelTimeEstimationContext);

            $travelFareCalculationContext = new TravelFareCalculationContext($passengers, $travelTime, $distance);
            $fare = DistanceAndTimeBasedFareCalculation::calculateFare($travelFareCalculationContext);

            $vehicle->profit = $fare - $vehicle->refueling_cost;

            if ($vehicle->fuel_type == 'Mild Hybrid')
            {
                $vehicle->effective_range = $vehicle->range + (50 * 0.5);
            }
            else
            {
                $vehicle->effective_range = $vehicle->range;
            }

            return $vehicle;
        });

        $suitableVehicles = $suitableVehicles->sortByDesc('profit');

        return response()->json($suitableVehicles->values());
    }
}
