<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(5, 50);
        $cost = fake()->numberBetween(50_000, 2_000_000);

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'spare_part_id' => SparePart::factory(),
            'qty' => $qty,
            'cost_idr' => $cost,
            'subtotal_idr' => $qty * $cost,
            'received_qty' => 0,
        ];
    }
}