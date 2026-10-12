<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, HasCode, SoftDeletes;

    protected const CODE_PREFIX = 'CUS';
    protected const CODE_LENGTH = 6;

    protected $fillable = [
        'user_id',
        'code',
        'address',
        'city',
        'province',
        'birth_date',
        'ktp_number',
        'loyalty_points',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'loyalty_points' => 'integer',
        ];
    }

    // ── Relationships ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(CustomerVehicle::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function serviceBookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }

    public function aiScans(): HasMany
    {
        return $this->hasMany(AiScan::class);
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function voucherRedemptions(): HasMany
    {
        return $this->hasMany(VoucherRedemption::class);
    }

    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    // ── Scopes ──

    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeInProvince($query, string $province)
    {
        return $query->where('province', $province);
    }

    public function scopeWithLoyaltyAbove($query, int $points)
    {
        return $query->where('loyalty_points', '>=', $points);
    }
}