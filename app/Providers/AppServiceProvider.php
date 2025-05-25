<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Utilities\ITravelTimeEstimationStrategy;
use App\Utilities\ITravelFareCalculationStrategy;
use App\Utilities\DistanceOnlyTravelTimeEstimation;
use App\Utilities\DistanceAndTimeBasedFareCalculation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ITravelTimeEstimationStrategy::class, DistanceOnlyTravelTimeEstimation::class);
        $this->app->bind(ITravelFareCalculationStrategy::class, DistanceAndTimeBasedFareCalculation::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
