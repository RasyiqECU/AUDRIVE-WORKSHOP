<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'topic',
        'message',
        'status',
        'read_at',
        'reply',
        'replied_by',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at'    => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    // ── Relationships ──

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    // ── Scopes ──

    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    public function scopeRead($query)
    {
        return $query->where('status', '!=', 'unread');
    }

    // ── Helpers ──

    public function markAsRead(): void
    {
        if ($this->status === 'unread') {
            $this->update([
                'status'  => 'read',
                'read_at' => now(),
            ]);
        }
    }
}
