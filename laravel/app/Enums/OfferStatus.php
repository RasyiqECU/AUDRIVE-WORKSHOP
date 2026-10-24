<?php

declare(strict_types=1);

namespace App\Enums;

enum OfferStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Menunggu',
            self::Accepted => 'Diterima',
            self::Rejected => 'Ditolak',
            self::Expired => 'Kadaluarsa',
            self::Cancelled => 'Dibatalkan',
        };
    }
}