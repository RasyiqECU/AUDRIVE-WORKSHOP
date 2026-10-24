<?php

namespace Database\Factories;

use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkOrderItemFactory extends Factory
{
    protected $model = WorkOrderItem::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['service', 'part', 'other']);
        $qty = fake()->numberBetween(1, 5);
        $price = fake()->numberBetween(50_000, 2_000_000);

        return [
            'work_order_id' => WorkOrder::factory(),
            'type' => $type,
            'description' => fake()->sentence(4),
            'spare_part_id' => $type === 'part' ? null : null,
            'qty' => $qty,
            'unit_price_idr' => $price,
            'subtotal_idr' => $qty * $price,
        ];
    }

    public function service(): static
    {
        return $this->state(fn () => ['type' => 'service']);
    }

    public function part(): static
    {
        return $this->state(fn () => ['type' => 'part']);
    }
}