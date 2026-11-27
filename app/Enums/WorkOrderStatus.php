<?php

declare(strict_types=1);

namespace App\Enums;

enum WorkOrderStatus: string
{
    case Booking = 'booking';
    case Queued = 'queued';
    case InProgress = 'in_progress';
    case WaitingPartConfirmation = 'waiting_part_confirmation';
    case QualityControl = 'quality_control';
    case Completed = 'completed';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Booking => 'Booking',
            self::Queued => 'Antrian',
            self::InProgress => 'Dikerjakan',
            self::WaitingPartConfirmation => 'Menunggu Konfirmasi Part',
            self::QualityControl => 'Quality Control',
            self::Completed => 'Selesai',
            self::Delivered => 'Diserahkan',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
