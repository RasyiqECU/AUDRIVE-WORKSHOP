<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VoucherType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'points_cost',
        'min_purchase_idr',
        'max_usage',
        'used_count',
        'expires_at',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'type' => VoucherType::class,
            'value' => 'integer',
            'points_cost' => 'integer',
            'min_purchase_idr' => 'integer',
            'max_usage' => 'integer',
            'used_count' => 'integer',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(VoucherRedemption::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     })
                     ->whereColumn('used_count', '<', 'max_usage');
    }

    public function isAvailable(): bool
    {
        return $this->is_active
            && $this->used_count < $this->max_usage
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}