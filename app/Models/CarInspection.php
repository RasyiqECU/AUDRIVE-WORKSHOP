<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_unit_id',
        'inspector_id',
        'checklist',
        'notes',
        'ai_scan_id',
        'result',
        'inspected_at',
    ];

    protected function casts(): array
    {
        return [
            'checklist' => 'array',
            'inspected_at' => 'datetime',
        ];
    }

    public function carUnit(): BelongsTo
    {
        return $this->belongsTo(CarUnit::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function aiScan(): BelongsTo
    {
        return $this->belongsTo(AiScan::class);
    }

    public function scopePassed($query)
    {
        return $query->where('result', 'pass');
    }

    public function scopeFailed($query)
    {
        return $query->where('result', 'fail');
    }
}