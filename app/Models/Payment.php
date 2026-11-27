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
