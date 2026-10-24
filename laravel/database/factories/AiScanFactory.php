<?php

namespace Database\Factories;

use App\Enums\AiScanStatus;
use App\Models\AiScan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiScanFactory extends Factory
{
    protected $model = AiScan::class;

    public function definition(): array
    {
        return [
            'customer_id' => null,
            'customer_vehicle_id' => null,
            'uploaded_by' => User::factory(),
            'audio_path' => 'ai-scans/' . fake()->uuid() . '.wav',
            'status' => fake()->randomElement(AiScanStatus::cases()),
            'predicted_class' => fake()->randomElement(['normal', 'bearing_wear', 'belt_issue', 'exhaust_leak', 'engine_knock', 'suspension_noise']),
            'confidence' => fake()->randomFloat(4, 0.5, 0.99),
            'recommendations' => [
                'action' => fake()->randomElement(['Lanjut pantau', 'Perlu servis', 'Segera perbaiki']),
                'detail' => fake()->sentence(),
            ],
            'spectrogram_path' => 'ai-scans/spectrograms/' . fake()->uuid() . '.png',
            'technician_review' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => AiScanStatus::Completed]);
    }

    public function needsReview(): static
    {
        return $this->state(fn () => ['status' => AiScanStatus::ManualReview]);
    }
}