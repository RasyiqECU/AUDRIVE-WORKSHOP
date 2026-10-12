<?php

namespace Database\Factories;

use App\Models\SparePartCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SparePartCategoryFactory extends Factory
{
    protected $model = SparePartCategory::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Mesin', 'Rem', 'Suspensi', 'Kelistrikan', 'Body', 'Interior',
            'Filter', 'Oli & Pelumas', 'Ban & Velg', 'AC', 'Transmisi',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 999),
            'description' => fake()->optional()->sentence(),
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}