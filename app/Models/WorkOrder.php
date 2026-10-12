<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WorkOrderStatus;
use App\Support\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use HasFactory, HasCode;

    protected const CODE_PREFIX = 'WO';
    protected const CODE_LENGTH = 6;

    protected $fillable = [
        'code',
        'service_booking_id',
        'customer_vehicle_id',
        'status',
        'assigned_mechanic_id',
        'started_at',
        'completed_at',
        'notes',
        'total_jasa_idr',
        'total_part_idr',
        'total_idr',
    ];

    protected function casts(): array
    {
        return [
            'status' => WorkOrderStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'total_jasa_idr' => 'integer',
            'total_part_idr' => 'integer',
            'total_idr' => 'integer',
        ];
    }

    public function serviceBooking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(CustomerVehicle::class, 'customer_vehicle_id');
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_mechanic_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', WorkOrderStatus::InProgress);
    }

    public function scopeQueued($query)
    {
        return $query->where('status', WorkOrderStatus::Queued);
    }

    public function scopeAssignedTo($query, int $mechanicId)
    {
        return $query->where('assigned_mechanic_id', $mechanicId);
    }

    public function recalculateTotals(): void
    {
        $this->total_jasa_idr = $this->items()->where('type', 'service')->sum('subtotal_idr');
        $this->total_part_idr = $this->items()->where('type', 'part')->sum('subtotal_idr');
        $this->total_idr = $this->total_jasa_idr + $this->total_part_idr;
        $this->save();
    }
}