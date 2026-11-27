<?php

namespace Database\Factories;

use App\Models\CustomerVehicle;
use App\Models\User;
use App\Models\WarrantyClaim;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WarrantyClaimFactory extends Factory
{
    protected $model = WarrantyClaim::class;

    public function definition(): array
    {
        return [
            'code' => 'WRN-'.strtoupper(Str::random(6)),
            'source_type' => 'service',
            'customer_vehicle_id' => CustomerVehicle::factory(),
            'customer_id' => fn (array $attributes) => CustomerVehicle::find($attributes['customer_vehicle_id'])->customer_id,
            'reference_date' => now()->subDays(5),
            'reference_km' => 10_000,
            'current_km' => 10_200,
            'complaint' => fake()->sentence(),
            'status' => 'submitted',
            'created_by' => User::factory()->sales(),
        ];
    }
}
