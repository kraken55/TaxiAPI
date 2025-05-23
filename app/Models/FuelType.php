<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelType extends Model
{
    protected $fillable = [
        'name',
        'price_per_kilometer',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
