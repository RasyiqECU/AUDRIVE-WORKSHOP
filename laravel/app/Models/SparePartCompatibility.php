<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparePartCompatibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'spare_part_id',
        'vehicle_brand',
        'vehicle_model',
        'year_from',
        'year_to',
    ];

    protected function casts(): array
    {
        return [
            'year_from' => 'integer',
            'year_to' => 'integer',
        ];
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }

    public function scopeForVehicle($query, string $brand, string $model, int $year)
    {
        return $query->where('vehicle_brand', $brand)
                     ->where('vehicle_model', $model)
                     ->where('year_from', '<=', $year)
                     ->where('year_to', '>=', $year);
    }

    public function getYearRangeAttribute(): string
    {
        return "{$this->year_from} - {$this->year_to}";
    }
}