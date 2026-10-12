<?php

namespace Database\Factories;

use App\Enums\SparePartStatus;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SparePartFactory extends Factory
{
    protected $model = SparePart::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Kampas Rem Depan', 'Filter Oli', 'Busi Iridium', 'V-Belt',
            'Shockbreaker Belakang', 'Aki 60Ah', 'Lampu LED H4', 'Oil Filter',
            'Timing Belt Kit', 'Water Pump', 'Brake Disc', 'Cabin Filter',
        ]);

        return [
            'sku' => 'SP-' . strtoupper(Str::random(6)),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 999),
            'category_id' => SparePartCategory::factory(),
            'brand' => fake()->randomElement(['OEM', 'Brembo', 'Bosch', 'NGK', 'Denso', 'Akebono', 'KYB']),
            'price_idr' => fake()->numberBetween(50_000, 5_000_000),
            'stock' => fake()->numberBetween(0, 100),
            'low_stock_threshold' => 5,
            'status' => fake()->randomElement(SparePartStatus::cases()),
            'description' => fake()->optional()->paragraph(),
            'image' => null,
            'weight_kg' => fake()->randomFloat(2, 0.1, 15),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => SparePartStatus::Active]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(0, $attributes['low_stock_threshold'] ?? 5),
        ]);
    }
}