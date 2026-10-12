<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerVehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerVehicleFactory extends Factory
{
    protected $model = CustomerVehicle::class;

    public function definition(): array
    {
        $brands = ['Lamborghini', 'Ferrari', 'Porsche', 'BMW', 'Mercedes-Benz', 'Audi', 'Toyota', 'Honda'];
        $brand = fake()->randomElement($brands);

        return [
            'customer_id' => Customer::factory(),
            'plate_number' => fake()->regexify('[A-Z]{2}####[A-Z]{3}'),
            'vin' => strtoupper(fake()->unique()->bothify('?????????????????')),
            'brand' => $brand,
            'model' => fake()->randomElement(['Huracán', 'Aventador', 'Urus', '488', 'F8', '911', 'Cayenne', 'X5', 'C-Class']),
            'year' => fake()->numberBetween(2015, 2025),
            'color' => fake()->randomElement(['Hitam', 'Putih', 'Merah', 'Biru', 'Abu-abu', 'Kuning']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}