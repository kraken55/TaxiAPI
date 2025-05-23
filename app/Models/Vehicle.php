<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Vehicle extends Model
{
    use HasFactory;

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
