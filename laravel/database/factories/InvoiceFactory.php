<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\SalesOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'invoiceable_type' => SalesOrder::class,
            'invoiceable_id' => SalesOrder::factory(),
            'number' => 'INV-' . date('Y') . '-' . strtoupper(fake()->unique()->regexify('[A-Z0-9]{6}')),
            'status' => fake()->randomElement(InvoiceStatus::cases()),
            'due_at' => fake()->dateTimeBetween('+7 days', '+30 days'),
            'total_idr' => fake()->numberBetween(500_000, 5_000_000_000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}