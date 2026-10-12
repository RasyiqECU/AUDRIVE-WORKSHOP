<?php

declare(strict_types=1);

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case PartiallyReceived = 'partially_received';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Sent => 'Terkirim',
            self::PartiallyReceived => 'Sebagian Diterima',
            self::Received => 'Diterima',
            self::Cancelled => 'Dibatalkan',
        };
    }
}