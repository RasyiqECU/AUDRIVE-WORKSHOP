<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use App\Support\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory, HasCode;

    protected const CODE_PREFIX = 'PO';
    protected const CODE_LENGTH = 6;

    protected $fillable = [
        'code',
        'supplier_name',
        'status',
        'total_idr',
        'ordered_at',
        'received_at',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PurchaseOrderStatus::class,
            'total_idr' => 'integer',
            'ordered_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', PurchaseOrderStatus::Draft);
    }

    public function scopeReceived($query)
    {
        return $query->where('status', PurchaseOrderStatus::Received);
    }

    public function recalculateTotal(): void
    {
        $this->total_idr = $this->items()->sum('subtotal_idr');
        $this->save();
    }
}