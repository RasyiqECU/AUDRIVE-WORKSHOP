<?php

namespace Database\Factories;

use App\Enums\OfferStatus;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'car_unit_id' => CarUnit::factory(),
            'customer_id' => Customer::factory(),
            'amount_idr' => fake()->numberBetween(300_000_000, 5_000_000_000),
            'status' => fake()->randomElement(OfferStatus::cases()),
            'note' => fake()->optional()->sentence(),
            'expires_at' => fake()->dateTimeBetween('now', '+7 days'),
            'reviewed_by' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => OfferStatus::Pending]);
    }
}