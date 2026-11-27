<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'paymentable_type',
        'paymentable_id',
        'method',
        'status',
        'amount_idr',
        'reference',
        'paid_at',
        'metadata',
        'purpose',
        'proof_path',
        'sender_name',
        'destination_bank',
        'uploaded_at',
        'verified_by',
        'verified_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount_idr' => 'integer',
            'paid_at' => 'datetime',
            'uploaded_at' => 'datetime',
            'verified_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /** Catat siapa yang menerima uang, dasar Laporan Shift Cashier. */
    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if ($payment->status === PaymentStatus::Success && in_array(auth()->user()?->role, ['cashier', 'super-admin'], true)) {
                $payment->verified_by ??= auth()->id();
            }
        });
    }

    /** Uang yang benar-benar diterima seorang kasir (termasuk yang kemudian direfund). */
    public function scopeReceivedBy($query, int $userId, $start = null, $end = null)
    {
        return $query->where('verified_by', $userId)
            ->where('method', '!=', 'refund')
            ->whereIn('status', [PaymentStatus::Success, PaymentStatus::Refunded])
            ->when($start && $end, fn ($q) => $q->whereBetween('paid_at', [$start, $end]));
    }

    public function paymentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', PaymentStatus::Success);
    }

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::Pending);
    }

    public function isSuccessful(): bool
    {
        return $this->status === PaymentStatus::Success;
    }
}
