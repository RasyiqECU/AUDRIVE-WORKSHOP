<?php

declare(strict_types=1);

namespace App\Enums;

enum CarUnitStatus: string
{
    case Draft = 'draft';
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';
    case InInspection = 'in_inspection';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Available => 'Tersedia',
            self::Reserved => 'Dipesan',
            self::Sold => 'Terjual',
            self::InInspection => 'Inspeksi',
            self::Inactive => 'Nonaktif',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft => 'neutral',
            self::Available => 'success',
            self::Reserved => 'warning',
            self::Sold => 'info',
            self::InInspection => 'warning',
            self::Inactive => 'danger',
        };
    }
}