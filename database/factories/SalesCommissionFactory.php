<?php

namespace Database\Factories;

use App\Models\SalesCommission;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesCommissionFactory extends Factory
{
    protected $model = SalesCommission::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->sales(),
            'sales_order_id' => SalesOrder::factory(),
            'amount_idr' => fake()->numberBetween(500_000, 50_000_000),
            'status' => fake()->randomElement(['pending', 'paid', 'pending']),
            'notes' => fake()->optional()->sentence(),
            'paid_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}