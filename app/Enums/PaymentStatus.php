<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case WaitingUpload = 'waiting_upload';
    case WaitingVerification = 'waiting_verification';
    case Rejected = 'rejected';
    case Success = 'success';
    case Failed = 'failed';
    case Expired = 'expired';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::WaitingUpload => 'Menunggu Bukti Transfer',
            self::WaitingVerification => 'Menunggu Verifikasi',
            self::Rejected => 'Bukti Ditolak',
            self::Success => 'Berhasil',
            self::Failed => 'Gagal',
            self::Expired => 'Kadaluarsa',
            self::Refunded => 'Dikembalikan',
        };
    }
}
