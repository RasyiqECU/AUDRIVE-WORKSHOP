<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'item_type',
        'car_unit_id',
        'spare_part_id',
        'description',
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

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function carUnit(): BelongsTo
    {
        return $this->belongsTo(CarUnit::class);
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }
}