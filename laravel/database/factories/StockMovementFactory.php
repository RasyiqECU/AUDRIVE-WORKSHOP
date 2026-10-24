<?php

namespace Database\Factories;

use App\Enums\StockMovementType;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        $type = fake()->randomElement(StockMovementType::cases());
        return [
            'spare_part_id' => SparePart::factory(),
            'type' => $type,
            'qty' => fake()->numberBetween(1, 20),
            'reference_type' => null,
            'reference_id' => null,
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}