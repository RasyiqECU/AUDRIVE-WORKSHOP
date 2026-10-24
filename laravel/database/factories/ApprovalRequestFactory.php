<?php

namespace Database\Factories;

use App\Enums\ApprovalStatus;
use App\Models\ApprovalRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApprovalRequestFactory extends Factory
{
    protected $model = ApprovalRequest::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['purchase_order', 'discount', 'refund', 'price_change']),
            'payload' => [
                'description' => fake()->sentence(),
                'amount' => fake()->numberBetween(1_000_000, 100_000_000),
            ],
            'requested_by' => User::factory(),
            'approved_by' => null,
            'status' => fake()->randomElement(ApprovalStatus::cases()),
            'reason' => fake()->optional()->paragraph(),
            'decided_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => ApprovalStatus::Pending]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => ApprovalStatus::Approved,
            'approved_by' => User::factory()->manager(),
            'decided_at' => now(),
        ]);
    }
}