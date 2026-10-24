<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoiceable_type',
        'invoiceable_id',
        'number',
        'status',
        'due_at',
        'total_idr',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'due_at' => 'datetime',
            'total_idr' => 'integer',
        ];
    }

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', InvoiceStatus::Overdue);
    }

    public function scopeDueBefore($query, $date)
    {
        return $query->where('due_at', '<', $date);
    }

    public function isOverdue(): bool
    {
        return $this->due_at && $this->due_at->isPast() && $this->status !== InvoiceStatus::Paid;
    }
}