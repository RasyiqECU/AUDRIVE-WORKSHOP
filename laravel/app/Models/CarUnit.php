<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CarUnitStatus;
use App\Support\HasCode;
use App\Support\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarUnit extends Model
{
    use HasFactory, HasCode, HasSlug, SoftDeletes;

    protected const CODE_PREFIX = 'CAR';
    protected const CODE_LENGTH = 6;

    protected $fillable = [
        'code',
        'slug',
        'brand',
        'model',
        'year',
        'transmission',
        'color',
        'mileage_km',
        'price_buy_idr',
        'price_sell_idr',
        'status',
        'description',
        'stock_status',
        'warranty_status',
        'stnk_status',
        'bpkb_status',
        'location',
        'featured_at',
        'fuel_type',
        'engine_cc',
        'exterior_color',
        'interior_color',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'mileage_km' => 'integer',
            'price_buy_idr' => 'integer',
            'price_sell_idr' => 'integer',
            'status' => CarUnitStatus::class,
            'featured_at' => 'datetime',
        ];
    }

    // ── Relationships ──

    public function photos(): HasMany
    {
        return $this->hasMany(CarPhoto::class)->orderBy('sort_order');
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(CarPhoto::class)->where('is_primary', true);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(CarInspection::class);
    }

    public function latestInspection(): HasOne
    {
        return $this->hasOne(CarInspection::class)->latestOfMany('inspected_at');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function salesOrderItems(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    // ── Scopes ──

    public function scopeAvailable($query)
    {
        return $query->where('status', CarUnitStatus::Available);
    }

    public function scopeFeatured($query)
    {
        return $query->whereNotNull('featured_at')->orderByDesc('featured_at');
    }

    public function scopeByBrand($query, string $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopeByYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopePriceRange($query, int $min, int $max)
    {
        return $query->whereBetween('price_sell_idr', [$min, $max]);
    }

    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('brand', 'like', "%{$keyword}%")
              ->orWhere('model', 'like', "%{$keyword}%")
              ->orWhere('code', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    // ── Accessors ──

    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->brand} {$this->model}";
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_sell_idr, 0, ',', '.');
    }

    public function getMarginAttribute(): int
    {
        return $this->price_sell_idr - $this->price_buy_idr;
    }
}