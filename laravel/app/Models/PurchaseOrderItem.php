<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'spare_part_id',
        'qty',
        'cost_idr',
        'subtotal_idr',
        'received_qty',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'cost_idr' => 'integer',
            'subtotal_idr' => 'integer',
            'received_qty' => 'integer',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }

    public function getRemainingQtyAttribute(): int
    {
        return $this->qty - $this->received_qty;
    }
}