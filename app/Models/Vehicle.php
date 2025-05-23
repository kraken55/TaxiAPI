<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'passenger_capacity',
        'range',
        'fuel_type_id',
    ];

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }
}
