<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'reviewer_type' => Customer::class,
            'reviewer_id' => Customer::factory(),
            'reviewable_type' => 'App\\Models\\CarUnit',
            'reviewable_id' => fake()->numberBetween(1, 50),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => 'approved']);
    }
}