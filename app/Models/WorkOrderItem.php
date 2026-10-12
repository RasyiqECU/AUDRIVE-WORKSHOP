<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'type',
        'description',
        'spare_part_id',
        'qty',
        'unit_price_idr',
        'subtotal_idr',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price_idr' => 'integer',
            'subtotal_idr' => 'integer',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }

    public function scopeServices($query)
    {
        return $query->where('type', 'service');
    }

    public function scopeParts($query)
    {
        return $query->where('type', 'part');
    }
}