<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Support\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory, HasCode;

    protected const CODE_PREFIX = 'TKT';
    protected const CODE_LENGTH = 5;

    protected $fillable = [
        'code',
        'customer_id',
        'subject',
        'status',
        'priority',
        'description',
        'resolved_at',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->oldest();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', TicketStatus::Open);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', TicketPriority::High);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }
}