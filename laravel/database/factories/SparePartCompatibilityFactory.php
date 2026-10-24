<?php

namespace Database\Factories;

use App\Models\SparePart;
use App\Models\SparePartCompatibility;
use Illuminate\Database\Eloquent\Factories\Factory;

class SparePartCompatibilityFactory extends Factory
{
    protected $model = SparePartCompatibility::class;

    public function definition(): array
    {
        return [
            'spare_part_id' => SparePart::factory(),
            'vehicle_brand' => fake()->randomElement(['Toyota', 'Honda', 'BMW', 'Mercedes-Benz', 'Lamborghini']),
            'vehicle_model' => fake()->randomElement(['Avanza', 'Civic', 'X5', 'C-Class', 'Huracán']),
            'year_from' => fake()->numberBetween(2010, 2020),
            'year_to' => fake()->numberBetween(2021, 2025),
        ];
    }
}