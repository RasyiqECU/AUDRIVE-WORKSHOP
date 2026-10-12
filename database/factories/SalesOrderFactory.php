<?php

namespace Database\Factories;

use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SalesOrderFactory extends Factory
{
    protected $model = SalesOrder::class;

    public function definition(): array
    {
        $subtotal = fake()->numberBetween(500_000, 5_000_000_000);
        $discount = fake()->numberBetween(0, $subtotal * 0.1);
        $tax = (int)(($subtotal - $discount) * 0.11);

        return [
            'code' => 'SO-' . strtoupper(Str::random(6)),
            'customer_id' => Customer::factory(),
            'type' => fake()->randomElement(SalesOrderType::cases()),
            'status' => fake()->randomElement(SalesOrderStatus::cases()),
            'subtotal_idr' => $subtotal,
            'discount_idr' => $discount,
            'tax_idr' => $tax,
            'total_idr' => $subtotal - $discount + $tax,
            'paid_idr' => 0,
            'payment_status' => 'pending',
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }

    public function carSales(): static
    {
        return $this->state(fn () => ['type' => SalesOrderType::CarSales]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $a) => [
            'status' => SalesOrderStatus::Paid,
            'payment_status' => 'paid',
            'paid_idr' => $a['total_idr'],
        ]);
    }
}