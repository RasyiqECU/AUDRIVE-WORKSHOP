<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SparePartStatus;
use App\Support\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePart extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'category_id',
        'brand',
        'price_idr',
        'stock',
        'low_stock_threshold',
        'status',
        'description',
        'image',
        'weight_kg',
    ];

    protected function casts(): array
    {
        return [
            'price_idr' => 'integer',
            'stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'status' => SparePartStatus::class,
            'weight_kg' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SparePartCategory::class, 'category_id');
    }

    public function compatibilities(): HasMany
    {
        return $this->hasMany(SparePartCompatibility::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function salesOrderItems(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function workOrderItems(): HasMany
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('status', SparePartStatus::Active);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'low_stock_threshold');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('sku', 'like', "%{$keyword}%")
              ->orWhere('brand', 'like', "%{$keyword}%");
        });
    }

    // ── Helpers ──

    public function isLowStock(): bool
    {
        return $this->stock <= $this->low_stock_threshold;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_idr, 0, ',', '.');
    }
}