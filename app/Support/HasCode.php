<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

trait HasCode
{
    protected static function bootHasCode(): void
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        $prefix = static::CODE_PREFIX ?? 'AUD';
        $length = static::CODE_LENGTH ?? 8;

        do {
            $code = $prefix . '-' . strtoupper(Str::random($length));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}