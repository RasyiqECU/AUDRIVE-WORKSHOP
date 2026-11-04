<?php

declare(strict_types=1);

namespace App\Support;

class Terbilang
{
    /**
     * Convert an integer number into Indonesian spoken words.
     */
    public static function make(int|float $number, string $suffix = 'Rupiah'): string
    {
        $number = abs((int) $number);
        if ($number === 0) {
            return trim("Nol {$suffix}");
        }

        $words = self::spell($number);

        return trim(ucwords($words).($suffix ? ' '.$suffix : ''));
    }

    protected static function spell(int $number): string
    {
        $ones = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas',
        ];

        if ($number < 12) {
            return $ones[$number];
        }

        if ($number < 20) {
            return self::spell($number - 10).' belas';
        }

        if ($number < 100) {
            $ten = (int) ($number / 10);
            $rest = $number % 10;

            return self::spell($ten).' puluh'.($rest ? ' '.self::spell($rest) : '');
        }

        if ($number < 200) {
            $rest = $number - 100;

            return 'seratus'.($rest ? ' '.self::spell($rest) : '');
        }

        if ($number < 1000) {
            $hundred = (int) ($number / 100);
            $rest = $number % 100;

            return self::spell($hundred).' ratus'.($rest ? ' '.self::spell($rest) : '');
        }

        if ($number < 2000) {
            $rest = $number - 1000;

            return 'seribu'.($rest ? ' '.self::spell($rest) : '');
        }

        if ($number < 1000000) {
            $thousand = (int) ($number / 1000);
            $rest = $number % 1000;

            return self::spell($thousand).' ribu'.($rest ? ' '.self::spell($rest) : '');
        }

        if ($number < 1000000000) {
            $million = (int) ($number / 1000000);
            $rest = $number % 1000000;

            return self::spell($million).' juta'.($rest ? ' '.self::spell($rest) : '');
        }

        if ($number < 1000000000000) {
            $billion = (int) ($number / 1000000000);
            $rest = $number % 1000000000;

            return self::spell($billion).' miliar'.($rest ? ' '.self::spell($rest) : '');
        }

        $trillion = (int) ($number / 1000000000000);
        $rest = $number % 1000000000000;

        return self::spell($trillion).' triliun'.($rest ? ' '.self::spell($rest) : '');
    }
}
