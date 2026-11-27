<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WorkOrderStatus;
use App\Support\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarrantyClaim extends Model
{
    use HasCode, HasFactory;

    protected const CODE_PREFIX = 'WRN';

    protected const CODE_LENGTH = 6;

    protected $fillable = [
        'code',
        'source_type',
        'customer_id',
        'customer_vehicle_id',
        'source_work_order_item_id',
        'source_sales_order_id',
        'reference_date',
        'reference_km',
        'current_km',
        'complaint',
        'status',
        'diagnosis',
        'is_covered',
        'diagnosed_by',
        'decided_by',
        'decided_at',
        'rejection_reason',
        'repair_work_order_id',
        'created_by',
    ];

    protected $attributes = [
        'status' => 'submitted',
    ];

    protected function casts(): array
    {
        return [
            'reference_date' => 'date',
            'reference_km' => 'integer',
            'current_km' => 'integer',
            'is_covered' => 'boolean',
            'decided_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(CustomerVehicle::class, 'customer_vehicle_id');
    }

    public function sourceItem(): BelongsTo
    {
        return $this->belongsTo(WorkOrderItem::class, 'source_work_order_item_id');
    }

    public function sourceOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'source_sales_order_id');
    }

    public function repairWorkOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'repair_work_order_id');
    }

    public function diagnoser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diagnosed_by');
    }

    public function statusLabel(): string
    {
        if ($this->status === 'approved') {
            return $this->repairWorkOrder?->status === WorkOrderStatus::Delivered
                ? 'Selesai & diserahkan'
                : 'Disetujui, dalam pengerjaan';
        }

        return match ($this->status) {
            'submitted' => 'Menunggu diagnosa kepala mekanik',
            'diagnosed' => 'Menunggu keputusan Manager',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }
}
