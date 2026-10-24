<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        return [
            'code' => 'PO-' . strtoupper(Str::random(6)),
            'supplier_name' => fake()->company(),
            'status' => fake()->randomElement(PurchaseOrderStatus::cases()),
            'total_idr' => fake()->numberBetween(1_000_000, 100_000_000),
            'ordered_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'received_at' => null,
            'created_by' => User::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}