<?php

namespace Database\Factories;

use App\Enums\CarUnitStatus;
use App\Models\CarUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CarUnitFactory extends Factory
{
    protected $model = CarUnit::class;

    public function definition(): array
    {
        $brand = fake()->randomElement(['Lamborghini', 'Ferrari', 'Porsche', 'BMW', 'Mercedes-Benz', 'Audi']);
        $model = fake()->randomElement(['Huracán', 'Aventador', 'Urus', '488', 'F8', '911', 'Cayenne', 'X5', 'C-Class', 'R8']);
        $year = fake()->numberBetween(2018, 2025);
        $priceBuy = fake()->numberBetween(500_000_000, 3_000_000_000);

        return [
            'code' => 'CAR-' . strtoupper(Str::random(6)),
            'slug' => Str::slug("{$year} {$brand} {$model}") . '-' . fake()->unique()->numberBetween(1000, 9999),
            'brand' => $brand,
            'model' => $model,
            'year' => $year,
            'transmission' => fake()->randomElement(['manual', 'automatic', 'cvt']),
            'color' => fake()->randomElement(['Nero Noctis', 'Bianco Monocerus', 'Rosso Mars', 'Giallo Orion', 'Verde Mantis', 'Blu Cepheus']),
            'mileage_km' => fake()->numberBetween(1000, 80000),
            'price_buy_idr' => $priceBuy,
            'price_sell_idr' => $priceBuy + fake()->numberBetween(50_000_000, 500_000_000),
            'status' => fake()->randomElement(CarUnitStatus::cases()),
            'description' => fake()->paragraphs(2, true),
            'stock_status' => 'available',
            'warranty_status' => fake()->randomElement(['active', 'expired', null]),
            'stnk_status' => fake()->randomElement(['valid', 'expired']),
            'bpkb_status' => fake()->randomElement(['available', 'missing']),
            'location' => fake()->randomElement(['Showroom Jakarta', 'Showroom Bandung', 'Warehouse']),
            'fuel_type' => fake()->randomElement(['bensin', 'hybrid', 'listrik']),
            'engine_cc' => fake()->randomElement(['3000', '4000', '5200', '6500']),
            'featured_at' => fake()->optional(0.3)->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function available(): static
    {
        return $this->state(fn () => ['status' => CarUnitStatus::Available]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['featured_at' => now()]);
    }
}