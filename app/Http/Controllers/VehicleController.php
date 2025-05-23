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
}
