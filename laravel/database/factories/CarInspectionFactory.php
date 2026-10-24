<?php

namespace Database\Factories;

use App\Models\CarInspection;
use App\Models\CarUnit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarInspectionFactory extends Factory
{
    protected $model = CarInspection::class;

    public function definition(): array
    {
        return [
            'car_unit_id' => CarUnit::factory(),
            'inspector_id' => User::factory(),
            'checklist' => [
                'exterior' => fake()->randomElement(['baik', 'sedang', 'buruk']),
                'interior' => fake()->randomElement(['baik', 'sedang', 'buruk']),
                'mesin' => fake()->randomElement(['baik', 'sedang', 'buruk']),
                'kaki_kaki' => fake()->randomElement(['baik', 'sedang', 'buruk']),
                'listrik' => fake()->randomElement(['baik', 'sedang', 'buruk']),
            ],
            'notes' => fake()->optional()->paragraph(),
            'ai_scan_id' => null,
            'result' => fake()->randomElement(['pass', 'fail', 'conditional']),
            'inspected_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function passed(): static
    {
        return $this->state(fn () => ['result' => 'pass']);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['result' => 'fail']);
    }
}