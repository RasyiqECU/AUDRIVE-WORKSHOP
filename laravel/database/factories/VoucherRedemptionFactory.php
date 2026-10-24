<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherRedemptionFactory extends Factory
{
    protected $model = VoucherRedemption::class;

    public function definition(): array
    {
        return [
            'voucher_id' => Voucher::factory(),
            'customer_id' => Customer::factory(),
            'sales_order_id' => null,
            'redeemed_at' => now(),
        ];
    }
}