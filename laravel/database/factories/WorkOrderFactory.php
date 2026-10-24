<?php

namespace Database\Factories;

use App\Enums\WorkOrderStatus;
use App\Models\CustomerVehicle;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        return [
            'code' => 'WO-' . strtoupper(Str::random(6)),
            'service_booking_id' => null,
            'customer_vehicle_id' => CustomerVehicle::factory(),
            'status' => fake()->randomElement(WorkOrderStatus::cases()),
            'assigned_mechanic_id' => User::factory()->mechanic(),
            'started_at' => fake()->optional()->dateTimeBetween('-7 days', 'now'),
            'completed_at' => null,
            'notes' => fake()->optional()->paragraph(),
            'total_jasa_idr' => fake()->numberBetween(200_000, 5_000_000),
            'total_part_idr' => fake()->numberBetween(100_000, 10_000_000),
            'total_idr' => 0,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => WorkOrderStatus::InProgress,
            'started_at' => now()->subHours(2),
        ]);
    }
}