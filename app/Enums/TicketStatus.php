<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match($this) {
            self::Open => 'Terbuka',
            self::InProgress => 'Diproses',
            self::Resolved => 'Terselesaikan',
            self::Closed => 'Ditutup',
        };
    }
}