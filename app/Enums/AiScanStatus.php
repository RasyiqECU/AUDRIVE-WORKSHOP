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
    case Reviewed = 'reviewed';

    public function label(): string
    {
        return match($this) {
            self::Pending       => 'Menunggu',
            self::Processing    => 'Diproses',
            self::Completed     => 'Selesai',
            self::Failed        => 'Gagal',
            self::ManualReview  => 'Selesai (Confidence Rendah)',
            self::Reviewed      => 'Terverifikasi Mekanik',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Completed    => 'text-verde border-verde',
            self::Reviewed     => 'text-teal-400 border-teal-400',
            self::Processing   => 'text-blue-400 border-blue-400',
            self::Pending      => 'text-giallo border-giallo',
            self::ManualReview => 'text-arancio border-arancio',
            self::Failed       => 'text-danger border-danger',
        };
    }
}