<?php

namespace App\Enums;

use App\Utilities\MildHybridRangeCalculationStrategy;
use App\Utilities\StandardRangeCalculationStrategy;
enum FuelTypeEnum: string
{
    case ELECTRIC = 'Electric';
    case GASOLINE = 'Gasoline';
    case MILD_HYBRID = 'Mild Hybrid';

    public function getRangeCalculationStrategy(): string
    {
        return match ($this) {
            self::MILD_HYBRID => MildHybridRangeCalculationStrategy::class,
            default => StandardRangeCalculationStrategy::class,
        };
    }
}
