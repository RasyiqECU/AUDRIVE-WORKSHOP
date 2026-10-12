<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyTransactionFactory extends Factory
{
    protected $model = LoyaltyTransaction::class;

    public function definition(): array
    {
        $isEarn = fake()->boolean(70);
        return [
            'customer_id' => Customer::factory(),
            'type' => $isEarn ? 'earning' : 'redemption',
            'points' => $isEarn ? fake()->numberBetween(10, 500) : -fake()->numberBetween(10, 200),
            'reference_type' => null,
            'reference_id' => null,
            'description' => fake()->sentence(),
        ];
    }
}