<?php

namespace Database\Factories;

use App\Enums\PromotionType;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true) . ' Promo',
            'slug' => Str::slug(fake()->unique()->words(3, true)),
            'type' => fake()->randomElement(PromotionType::cases()),
            'value' => fake()->numberBetween(5, 50),
            'starts_at' => now()->subDays(7),
            'ends_at' => now()->addDays(30),
            'is_active' => true,
            'description' => fake()->sentence(),
            'applicable_to' => fake()->randomElement(['all', 'cars', 'spare_parts', 'service']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'is_active' => true,
        ]);
    }
}