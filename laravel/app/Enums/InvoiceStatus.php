<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Sent => 'Terkirim',
            self::Paid => 'Dibayar',
            self::Overdue => 'Jatuh Tempo',
            self::Cancelled => 'Dibatalkan',
        };
    }
}