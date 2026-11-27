<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Support\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class SalesOrder extends Model
{
    use HasCode, HasFactory;

    protected const CODE_PREFIX = 'SO';

    protected const CODE_LENGTH = 6;

    protected $fillable = [
        'code',
        'customer_id',
        'type',
        'status',
        'subtotal_idr',
        'discount_idr',
        'tax_idr',
        'total_idr',
        'paid_idr',
        'payment_status',
        'notes',
        'created_by',
        'work_order_id',
        'payment_channel',
        'fulfillment_method',
        'shipping_address',
        'shipping_fee_idr',
        'stock_reserved',
        'reserved_until',
        'prepared_at',
        'handed_over_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => SalesOrderType::class,
            'status' => SalesOrderStatus::class,
            'subtotal_idr' => 'integer',
            'discount_idr' => 'integer',
            'tax_idr' => 'integer',
            'total_idr' => 'integer',
            'paid_idr' => 'integer',
            'shipping_fee_idr' => 'integer',
            'stock_reserved' => 'boolean',
            'reserved_until' => 'datetime',
            'prepared_at' => 'datetime',
            'handed_over_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'paymentable_id')
            ->where('paymentable_type', SalesOrder::class);
    }

    public function invoice(): MorphOne
    {
        return $this->morphOne(Invoice::class, 'invoiceable');
    }

    public function voucherRedemptions(): HasMany
    {
        return $this->hasMany(VoucherRedemption::class);
    }

    public function commission(): HasMany
    {
        return $this->hasMany(SalesCommission::class);
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', SalesOrderStatus::Pending);
    }

    public function scopePaid($query)
    {
        return $query->where('status', SalesOrderStatus::Paid);
    }

    public function scopeOfType($query, SalesOrderType $type)
    {
        return $query->where('type', $type);
    }

    // ── Helpers ──

    public function getRemainingBalanceAttribute(): int
    {
        return $this->total_idr - $this->paid_idr;
    }

    public function isOnlineTransfer(): bool
    {
        return $this->payment_channel === 'online_transfer';
    }

    /** Customer boleh mengunggah (ulang) bukti transfer. */
    public function awaitsProof(): bool
    {
        return $this->isOnlineTransfer()
            && $this->status === SalesOrderStatus::Pending
            && in_array($this->payment_status, [PaymentStatus::WaitingUpload->value, PaymentStatus::Rejected->value], true)
            && (! $this->reserved_until || $this->reserved_until->isFuture());
    }

    /** Status yang ditampilkan ke customer dan staf. */
    public function progressLabel(): string
    {
        if ($this->status === SalesOrderStatus::Pending) {
            return match (true) {
                $this->isOnlineTransfer() => PaymentStatus::tryFrom((string) $this->payment_status)?->label() ?? 'Menunggu Pembayaran',
                default => 'Menunggu Pembayaran di Kasir',
            };
        }

        if ($this->status === SalesOrderStatus::Paid && $this->type === SalesOrderType::SparePart) {
            return match (true) {
                $this->handed_over_at !== null => $this->fulfillment_method === 'delivery' ? 'Dikirim' : 'Sudah Diambil',
                $this->prepared_at !== null => $this->fulfillment_method === 'delivery' ? 'Siap Dikirim' : 'Siap Diambil',
                default => 'Lunas - Sedang Disiapkan',
            };
        }

        return $this->status->label();
    }

    public function isFullyPaid(): bool
    {
        return $this->paid_idr >= $this->total_idr;
    }

    public function recalculateTotals(): void
    {
        $this->subtotal_idr = $this->items()->sum('subtotal_idr');
        $this->total_idr = $this->subtotal_idr - $this->discount_idr + $this->tax_idr;
        $this->save();
    }
}
