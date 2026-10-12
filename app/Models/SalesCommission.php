<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sales_order_id',
        'amount_idr',
        'status',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_idr' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}