<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => fake()->dateTimeBetween('-30 days', 'now'),
            'clock_in' => fake()->randomElement(['07:00', '07:30', '08:00', '08:15', '08:30']),
            'clock_out' => fake()->randomElement(['16:00', '16:30', '17:00', '17:30']),
            'status' => fake()->randomElement(['present', 'late', 'present', 'present']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function present(): static
    {
        return $this->state(fn () => ['status' => 'present']);
    }

    public function late(): static
    {
        return $this->state(fn () => ['status' => 'late']);
    }
}