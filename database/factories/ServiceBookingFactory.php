<?php

namespace Database\Factories;

use App\Enums\ServiceBookingStatus;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\ServiceBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceBookingFactory extends Factory
{
    protected $model = ServiceBooking::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'customer_vehicle_id' => CustomerVehicle::factory(),
            'service_type' => fake()->randomElement(['regular_service', 'heavy_repair', 'body_repair', 'ac_service', 'inspection']),
            'preferred_date' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'preferred_time' => fake()->randomElement(['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00']),
            'status' => fake()->randomElement(ServiceBookingStatus::cases()),
            'estimated_cost_idr' => fake()->numberBetween(500_000, 15_000_000),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => ServiceBookingStatus::Pending]);
    }
}