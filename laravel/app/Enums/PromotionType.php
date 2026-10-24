<?php

declare(strict_types=1);

namespace App\Enums;

enum PromotionType: string
{
    case Fixed = 'fixed';
    case Percent = 'percent';

    public function label(): string
    {
        return match($this) {
            self::Fixed => 'Nominal Tetap',
            self::Percent => 'Persentase',
        };
    }
}