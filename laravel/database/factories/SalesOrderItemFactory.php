<?php

namespace Database\Factories;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesOrderItemFactory extends Factory
{
    protected $model = SalesOrderItem::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 3);
        $price = fake()->numberBetween(100_000, 5_000_000);

        return [
            'sales_order_id' => SalesOrder::factory(),
            'item_type' => fake()->randomElement(['car', 'spare_part', 'service']),
            'car_unit_id' => null,
            'spare_part_id' => null,
            'description' => fake()->sentence(3),
            'qty' => $qty,
            'unit_price_idr' => $price,
            'subtotal_idr' => $qty * $price,
        ];
    }
}