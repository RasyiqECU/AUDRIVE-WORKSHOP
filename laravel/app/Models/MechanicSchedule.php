<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MechanicSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'shift_start',
        'shift_end',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_available' => 'boolean',
        ];
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForWeek($query, $startDate)
    {
        return $query->whereBetween('date', [
            $startDate,
            $startDate->copy()->addDays(6),
        ]);
    }
}