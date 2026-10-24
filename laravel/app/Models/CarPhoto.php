<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_unit_id',
        'path',
        'is_primary',
        'sort_order',
        'alt_text',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ── Relationships ──

    public function carUnit(): BelongsTo
    {
        return $this->belongsTo(CarUnit::class);
    }

    // ── Scopes ──

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // ── Accessors ──

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    public function getPhotoPathAttribute(): ?string
    {
        return $this->path;
    }
}