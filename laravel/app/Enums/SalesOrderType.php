<?php

declare(strict_types=1);

namespace App\Enums;

enum SalesOrderType: string
{
    case CarSales = 'car_sales';
    case Service = 'service';
    case SparePart = 'spare_part';
    case Pos = 'pos';

    public function label(): string
    {
        return match($this) {
            self::CarSales => 'Penjualan Mobil',
            self::Service => 'Servis',
            self::SparePart => 'Spare Part',
            self::Pos => 'POS',
        };
    }
}