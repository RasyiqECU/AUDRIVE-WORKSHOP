<?php

namespace Database\Factories;

use App\Models\MechanicSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MechanicScheduleFactory extends Factory
{
    protected $model = MechanicSchedule::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->mechanic(),
            'date' => fake()->dateTimeBetween('now', '+30 days'),
            'shift_start' => fake()->randomElement(['07:00', '08:00', '09:00']),
            'shift_end' => fake()->randomElement(['16:00', '17:00', '18:00']),
            'is_available' => true,
        ];
    }

    public function available(): static
    {
        return $this->state(fn () => ['is_available' => true]);
    }

    public function unavailable(): static
    {
        return $this->state(fn () => ['is_available' => false]);
    }
}