<?php


declare(strict_types=1);


namespace App\Services\Ai;


use App\Models\AiScan;
use App\Services\Ai\Contracts\AiScanServiceContract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class FastApiAiScanService implements AiScanServiceContract
{
    protected string $baseUrl;
    protected int $timeout;


    public function __construct()
    {
        $this->baseUrl = config('services.ai.base_url', 'http://localhost:8001');
        $this->timeout = config('services.ai.timeout', 3);
    }


    /**
     * {@inheritdoc}
     */
    public function analyze(string $audioPath): array
    {
        try {
            $fullPath = Storage::disk('public')->path($audioPath);


            if (!file_exists($fullPath)) {
                throw new \Exception("Audio file not found: {$audioPath}");
            }


            $response = Http::timeout($this->timeout)
                ->attach('audio', file_get_contents($fullPath), basename($audioPath))
                ->post("{$this->baseUrl}/api/v1/analyze");


            if (!$response->successful()) {
                throw new \Exception("AI service returned error: {$response->status()}");
            }


            $data = $response->json();


            return [
                'predicted_class' => $data['predicted_class'] ?? 'unknown',
                'confidence' => (float) ($data['confidence'] ?? 0.0),
                'recommendations' => $data['recommendations'] ?? [],
                'spectrogram_url' => $data['spectrogram_url'] ?? null,
            ];


        } catch (\Exception $e) {
            Log::error('AI Service Error', [
                'audio_path' => $audioPath,
                'error' => $e->getMessage(),
            ]);


            throw $e;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function processScan(AiScan $scan): void
    {
        try {
            $scan->update(['status' => 'processing']);


            $result = $this->analyze($scan->audio_path);


            $scan->update([
                'status' => 'completed',
                'predicted_class' => $result['predicted_class'],
                'confidence' => $result['confidence'],
                'recommendations' => $result['recommendations'],
                'spectrogram_path' => $result['spectrogram_url'],
            ]);


        } catch (\Exception $e) {
            // If AI service fails, mark as manual_review
            $scan->update([
                'status' => 'manual_review',
                'recommendations' => [
                    'AI service unavailable. Manual review required.',
                    'Error: ' . $e->getMessage(),
                ],
            ]);


            Log::warning('AI scan failed, marked for manual review', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}



