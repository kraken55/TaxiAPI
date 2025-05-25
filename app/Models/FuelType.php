<?php

namespace App\Models;

use App\Enums\FuelTypeEnum;
use Illuminate\Database\Eloquent\Model;

class FuelType extends Model
{
    protected $fillable = [
        'name',
        'price_per_kilometer',
    ];

    protected $casts = [
        'name' => FuelTypeEnum::class,
        'price_per_kilometer' => 'float',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
