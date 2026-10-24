<?php


declare(strict_types=1);


namespace App\Services;


use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class VoucherService
{
    /**
     * Validate voucher
     */
    public function validate(Voucher $voucher, Customer $customer, int $orderAmount): array
    {
        $errors = [];


        // Check if voucher is active
        if (!$voucher->is_active) {
            $errors[] = 'Voucher is not active';
        }


        // Check if voucher is expired
        if ($voucher->expires_at && $voucher->expires_at->isPast()) {
            $errors[] = 'Voucher has expired';
        }


        // Check if voucher has been used max times
        if ($voucher->used_count >= $voucher->max_usage) {
            $errors[] = 'Voucher has reached maximum usage';
        }


        // Check minimum purchase
        if ($orderAmount < $voucher->min_purchase) {
            $errors[] = 'Minimum purchase not met. Required: Rp ' . number_format($voucher->min_purchase);
        }


        // Check if customer has already redeemed this voucher
        $alreadyRedeemed = VoucherRedemption::where('voucher_id', $voucher->id)
            ->where('customer_id', $customer->id)
            ->exists();


        if ($alreadyRedeemed) {
            $errors[] = 'You have already used this voucher';
        }


        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }


    /**
     * Calculate discount amount
     */
    public function calculateDiscount(Voucher $voucher, int $orderAmount): int
    {
        if ($voucher->type === 'percentage') {
            $discount = (int) ($orderAmount * ($voucher->discount_value / 100));


            // Apply max discount if set
            if ($voucher->max_discount && $discount > $voucher->max_discount) {
                $discount = $voucher->max_discount;
            }


            return $discount;
        }


        // Fixed amount
        return $voucher->discount_value;
    }


    /**
     * Redeem voucher
     */
    public function redeem(Voucher $voucher, Customer $customer, SalesOrder $order): VoucherRedemption
    {
        return DB::transaction(function () use ($voucher, $customer, $order) {
            // Validate voucher
            $validation = $this->validate($voucher, $customer, $order->total_amount);


            if (!$validation['valid']) {
                throw new \Exception(implode(', ', $validation['errors']));
            }


            // Lock voucher for update
            $lockedVoucher = Voucher::where('id', $voucher->id)
                ->lockForUpdate()
                ->first();


            // Create redemption record
            $redemption = VoucherRedemption::create([
                'voucher_id' => $voucher->id,
                'customer_id' => $customer->id,
                'sales_order_id' => $order->id,
                'discount_amount' => $this->calculateDiscount($voucher, $order->total_amount),
            ]);


            // Increment usage count
            $lockedVoucher->increment('used_count');


            Log::info("Voucher redeemed", [
                'voucher_id' => $voucher->id,
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'discount' => $redemption->discount_amount,
            ]);


            return $redemption;
        });
    }


    /**
     * Get available vouchers for customer
     */
    public function getAvailableVouchers(Customer $customer): \Illuminate\Support\Collection
    {
        return Voucher::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->whereColumn('used_count', '<', 'max_usage')
            ->whereDoesntHave('redemptions', function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            })
            ->get();
    }


    /**
     * Get customer voucher history
     */
    public function getCustomerHistory(Customer $customer): \Illuminate\Support\Collection
    {
        return VoucherRedemption::where('customer_id', $customer->id)
            ->with('voucher')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}



