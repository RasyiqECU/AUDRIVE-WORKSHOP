<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'plate_number',
        'vin',
        'brand',
        'model',
        'year',
        'color',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
        ];
    }

    // ── Relationships ──

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function serviceBookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function aiScans(): HasMany
    {
        return $this->hasMany(AiScan::class);
    }

    // ── Scopes ──

    public function scopeByBrand($query, string $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopeByPlate($query, string $plate)
    {
        return $query->where('plate_number', $plate);
    }

    public function scopeYearBetween($query, int $from, int $to)
    {
        return $query->whereBetween('year', [$from, $to]);
    }

    // ── Accessors ──

    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->brand} {$this->model}";
    }

    public function getLicensePlateAttribute(): ?string
    {
        return $this->plate_number;
    }
}