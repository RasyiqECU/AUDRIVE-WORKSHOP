<?php

declare(strict_types=1);

namespace App\Enums;

enum AiScanStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
    case ManualReview = 'manual_review';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Menunggu',
            self::Processing => 'Diproses',
            self::Completed => 'Selesai',
            self::Failed => 'Gagal',
            self::ManualReview => 'Review Manual',
        };
    }
}