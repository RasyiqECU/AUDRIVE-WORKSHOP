<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\SalesOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'paymentable_type' => SalesOrder::class,
            'paymentable_id' => SalesOrder::factory(),
            'method' => fake()->randomElement(['bank_transfer', 'credit_card', 'cash', 'ewallet']),
            'status' => fake()->randomElement(PaymentStatus::cases()),
            'amount_idr' => fake()->numberBetween(500_000, 5_000_000_000),
            'reference' => 'PAY-' . strtoupper(fake()->unique()->regexify('[A-Z0-9]{10}')),
            'paid_at' => fake()->optional(0.7)->dateTimeBetween('-1 month', 'now'),
            'metadata' => null,
        ];
    }

    public function success(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::Success,
            'paid_at' => now(),
        ]);
    }
}