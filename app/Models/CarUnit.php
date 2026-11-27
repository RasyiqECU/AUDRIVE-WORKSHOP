<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CarUnitStatus;
use App\Support\HasCode;
use App\Support\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarUnit extends Model
{
    use HasCode, HasFactory, HasSlug, SoftDeletes;

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
        'condition',
        'ownership',
        'seller_customer_id',
        'condition_defects',
        'condition_additions',
        'seller_warranty_info',
        'seller_warranty_verified',
        'asking_price_idr',
        'min_price_idr',
        'commission_idr',
        'seller_payout_idr',
        'seller_paid_at',
        'seller_paid_by',
        'price_approved_by',
        'price_approved_at',
        'repair_cost_idr',
    ];

    protected $attributes = [
        'condition' => 'used',
        'ownership' => 'dealer',
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
            'seller_warranty_verified' => 'boolean',
            'asking_price_idr' => 'integer',
            'min_price_idr' => 'integer',
            'commission_idr' => 'integer',
            'seller_payout_idr' => 'integer',
            'seller_paid_at' => 'datetime',
            'price_approved_at' => 'datetime',
            'repair_cost_idr' => 'integer',
        ];
    }

    /** Lepas status Dipesan bila tidak ada pesanan yang mengklaim unit ini. */
    public function releaseIfUnclaimed(): void
    {
        $claimed = $this->salesOrderItems()
            ->whereHas('salesOrder', fn ($q) => $q->where('status', 'paid')
                ->orWhere(fn ($pending) => $pending->where('status', 'pending')->where(fn ($p) => $p->where('deposit_idr', 0)->orWhere('paid_idr', '>', 0))))
            ->exists();

        if ($this->status === CarUnitStatus::Reserved && ! $claimed) {
            $this->update(['status' => CarUnitStatus::Available]);
        }
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'seller_customer_id');
    }

    public function isConsignment(): bool
    {
        return $this->ownership === 'consignment';
    }

    public function isSoldBy(Customer $customer): bool
    {
        return $this->seller_customer_id === $customer->id;
    }

    public function getConditionLabelAttribute(): string
    {
        if ($this->isConsignment()) {
            return 'Bekas - Titip Jual';
        }

        return $this->condition === 'new' ? 'Baru' : 'Bekas';
    }

    /** Titip jual yang belum disetujui (atau ditarik/ditolak) tidak tampil ke publik. */
    public function scopePubliclyListed($query)
    {
        return $query->where(fn ($q) => $q->where('ownership', 'dealer')
            ->orWhereNotIn('status', [CarUnitStatus::Draft, CarUnitStatus::InInspection, CarUnitStatus::Inactive]));
    }

    public function isPubliclyListed(): bool
    {
        return ! $this->isConsignment()
            || ! in_array($this->status, [CarUnitStatus::Draft, CarUnitStatus::InInspection, CarUnitStatus::Inactive], true);
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
        return 'Rp '.number_format($this->price_sell_idr, 0, ',', '.');
    }

    public function getMarginAttribute(): int
    {
        return $this->price_sell_idr - $this->price_buy_idr;
    }
}
