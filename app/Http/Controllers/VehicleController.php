<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

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
        $passengers = $request->input('passengers');
        $distance = $request->input('distance');

        $suitableVehicles = Vehicle::join('fuel_types', 'vehicles.fuel_type_id', '=', 'fuel_types.id')
            ->where('vehicles.passenger_capacity', '>=', $passengers)
            ->whereRaw('vehicles.range - (? * fuel_types.efficiency_ratio) >= 0', [$distance])
            ->selectRaw('vehicles.*,  (? * fuel_types.efficiency_ratio) as range_consumption', [$distance])
            ->get();

        return response()->json($suitableVehicles);
    }
}
