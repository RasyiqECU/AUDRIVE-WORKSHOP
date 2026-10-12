<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        return [
            'code' => 'TKT-' . strtoupper(Str::random(5)),
            'customer_id' => Customer::factory(),
            'subject' => fake()->sentence(4),
            'status' => fake()->randomElement(TicketStatus::cases()),
            'priority' => fake()->randomElement(TicketPriority::cases()),
            'description' => fake()->paragraphs(2, true),
            'resolved_at' => null,
            'assigned_to' => null,
        ];
    }

    public function open(): static
    {
        return $this->state(fn () => ['status' => TicketStatus::Open]);
    }

    public function highPriority(): static
    {
        return $this->state(fn () => ['priority' => TicketPriority::High]);
    }
}