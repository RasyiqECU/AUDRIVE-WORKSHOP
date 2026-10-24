<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ServiceBookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_vehicle_id',
        'service_type',
        'preferred_date',
        'preferred_time',
        'status',
        'estimated_cost_idr',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'estimated_cost_idr' => 'integer',
            'status' => ServiceBookingStatus::class,
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

    public function workOrder(): HasOne
    {
        return $this->hasOne(WorkOrder::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', ServiceBookingStatus::Pending);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', ServiceBookingStatus::Confirmed);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('preferred_date', $date);
    }
}