<?php

declare(strict_types=1);

namespace App\Services;

/** Rekening tujuan, ongkir, dan batas waktu bayar — satu sumber untuk semua halaman. */
class PaymentSettings
{
    /** Nilai bawaan; ganti dengan rekening asli di Konfigurasi Sistem. */
    public const DEFAULT_ACCOUNTS = "BCA | 8830 1928 3829 001 | PT AUDRIVE INDONESIA UTAMA\nMandiri | 168 00 9988 7712 | PT AUDRIVE INDONESIA UTAMA";

    public const DEFAULT_SHIPPING_FEE = 50_000;

    public const DEFAULT_DEADLINE_HOURS = 24;

    public const DEFAULT_BOOKING_FEE = 5_000_000;

    public const DEFAULT_SETTLEMENT_DAYS = 7;

    public const DEFAULT_TOWING_FEE = 750_000;

    public static function bookingFee(): int
    {
        return (int) SettingService::get('booking_fee_idr', self::DEFAULT_BOOKING_FEE);
    }

    public static function settlementDays(): int
    {
        return max(1, (int) SettingService::get('settlement_days', self::DEFAULT_SETTLEMENT_DAYS));
    }

    public static function towingFee(): int
    {
        return (int) SettingService::get('towing_fee_idr', self::DEFAULT_TOWING_FEE);
    }

    /** @return list<array{bank: string, number: string, holder: string}> */
    public static function accounts(): array
    {
        $accounts = [];
        foreach (preg_split('/\R/', self::accountsText()) as $line) {
            $parts = array_map('trim', explode('|', $line));
            if (count($parts) === 3 && $parts[0] !== '' && $parts[1] !== '') {
                $accounts[] = ['bank' => $parts[0], 'number' => $parts[1], 'holder' => $parts[2]];
            }
        }

        return $accounts;
    }

    public static function accountsText(): string
    {
        return (string) (SettingService::get('bank_accounts') ?: self::DEFAULT_ACCOUNTS);
    }

    public static function shippingFee(): int
    {
        return (int) SettingService::get('shipping_fee_idr', self::DEFAULT_SHIPPING_FEE);
    }

    public static function deadlineHours(): int
    {
        return max(1, (int) SettingService::get('payment_deadline_hours', self::DEFAULT_DEADLINE_HOURS));
    }
}
