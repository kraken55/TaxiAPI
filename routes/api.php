<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;

Route::get('/vehicles', [VehicleController::class, 'index']);
Route::get('/vehicles/suitable', [VehicleController::class, 'getSuitableVehicles']);
Route::get('/vehicles/{id}', [VehicleController::class, 'show']);
Route::post('/vehicles', [VehicleController::class, 'store']);
Route::patch('/vehicles/{id}', [VehicleController::class, 'update']);
Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy']);

