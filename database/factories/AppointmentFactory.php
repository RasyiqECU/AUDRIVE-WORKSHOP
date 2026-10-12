<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['test_drive', 'consultation', 'delivery', 'pickup']),
            'car_unit_id' => null,
            'customer_id' => Customer::factory(),
            'scheduled_at' => fake()->dateTimeBetween('now', '+14 days'),
            'status' => fake()->randomElement(AppointmentStatus::cases()),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function testDrive(): static
    {
        return $this->state(fn () => ['type' => 'test_drive']);
    }

    public function upcoming(): static
    {
        return $this->state(fn () => [
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+7 days'),
            'status' => AppointmentStatus::Confirmed,
        ]);
    }
}