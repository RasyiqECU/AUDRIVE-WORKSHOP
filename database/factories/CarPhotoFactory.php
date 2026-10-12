<?php

namespace Database\Factories;

use App\Models\CarPhoto;
use App\Models\CarUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarPhotoFactory extends Factory
{
    protected $model = CarPhoto::class;

    public function definition(): array
    {
        return [
            'car_unit_id' => CarUnit::factory(),
            'path' => 'cars/' . fake()->uuid() . '.jpg',
            'is_primary' => false,
            'sort_order' => fake()->numberBetween(0, 10),
            'alt_text' => fake()->optional()->sentence(3),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn () => ['is_primary' => true]);
    }
}