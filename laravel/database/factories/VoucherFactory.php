<?php

namespace Database\Factories;

use App\Enums\VoucherType;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        return [
            'code' => 'VCR-' . strtoupper(fake()->unique()->regexify('[A-Z0-9]{8}')),
            'type' => fake()->randomElement(VoucherType::cases()),
            'value' => fake()->numberBetween(10, 50),
            'min_purchase_idr' => fake()->numberBetween(0, 1_000_000),
            'max_usage' => fake()->numberBetween(10, 100),
            'used_count' => 0,
            'expires_at' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'is_active' => true,
            'description' => fake()->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'is_active' => true,
            'expires_at' => now()->addMonth(),
        ]);
    }
}