<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AiScanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_vehicle_id',
        'uploaded_by',
        'audio_path',
        'status',
        'predicted_class',
        'confidence',
        'recommendations',
        'spectrogram_path',
        'technician_review',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AiScanStatus::class,
            'confidence' => 'decimal:4',
            'recommendations' => 'array',
            'reviewed_at' => 'datetime',
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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', AiScanStatus::Pending);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', AiScanStatus::Completed);
    }

    public function scopeNeedsReview($query)
    {
        return $query->where('status', AiScanStatus::ManualReview);
    }

    public function isReviewed(): bool
    {
        return $this->reviewed_at !== null;
    }
}