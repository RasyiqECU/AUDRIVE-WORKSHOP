<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code' => 'CUS-' . strtoupper(fake()->unique()->regexify('[A-Z0-9]{6}')),
            'address' => fake()->address(),
            'city' => fake()->randomElement(['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang', 'Makassar']),
            'province' => 'DKI Jakarta',
            'birth_date' => fake()->dateTimeBetween('-50 years', '-18 years'),
            'ktp_number' => fake()->numerify('##############'),
            'loyalty_points' => fake()->numberBetween(0, 5000),
        ];
    }
}