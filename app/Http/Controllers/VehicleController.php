<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Services\GetSuitableVehiclesService;

class VehicleController extends Controller
{
    private GetSuitableVehiclesService $getSuitableVehiclesService;

    public function __construct(GetSuitableVehiclesService $getSuitableVehiclesService)
    {
        $this->getSuitableVehiclesService = $getSuitableVehiclesService;
    }

    public function index()
    {
        $vehicles = Vehicle::all();
        return response()->json($vehicles);
    }

    public function show($id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle)
        {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }
        return response()->json($vehicle);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'passenger_capacity' => 'required|integer|min:1',
            'range' => 'required|numeric|min:0',
            'fuel_type_id' => 'required|exists:fuel_types,id',
        ]);
        $vehicle = Vehicle::create($validatedData);
        return response()->json($vehicle, 201);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle)
        {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }
        $validatedData = $request->validate([
            'passenger_capacity' => 'sometimes|required|integer|min:1',
            'range' => 'sometimes|required|numeric|min:0',
            'fuel_type_id' => 'sometimes|required|exists:fuel_types,id',
        ]);
        $vehicle->update($validatedData);
        return response()->json($vehicle);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle)
        {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }
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


        $suitableVehicles = $this->getSuitableVehiclesService->findSuitableVehicles($passengers, $distance);

        return response()->json($suitableVehicles->values());
    }
}
