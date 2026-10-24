<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AiScanStatus;
use App\Models\AiScan;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * AiScanService
 * Implementasi Langkah 1–3 dari workflow diagnostik.
 * Langkah 4 (manual review) ditangani oleh AiScanReviewController.
 */
class AiScanService
{
    private string $baseUrl;
    private string $secret;
    private int    $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('ai_service.url', 'http://localhost:8001'), '/');
        $this->secret  = config('ai_service.secret', '');
        $this->timeout = (int) config('ai_service.timeout', 60);
    }

    /**
     * Pipeline lengkap: upload → AI → simpan hasil.
     */
    public function process(AiScan $scan): void
    {
        $scan->update(['status' => AiScanStatus::Processing]);

        try {
            $response = $this->callPredictApi($scan);
            $this->handleResponse($scan, $response);

        } catch (ConnectionException $e) {
            Log::error("AiScan #{$scan->id}: Tidak bisa terhubung ke AI service", [
                'url' => $this->baseUrl, 'error' => $e->getMessage(),
            ]);
            $scan->update(['status' => AiScanStatus::Failed]);
            throw $e;

        } catch (\Throwable $e) {
            Log::error("AiScan #{$scan->id}: Gagal", ['error' => $e->getMessage()]);
            $scan->update(['status' => AiScanStatus::Failed]);
            throw $e;
        }
    }

    /**
     * Kirim audio ke Python /predict.
     */
    private function callPredictApi(AiScan $scan): array
    {
        $audioPath = Storage::disk('public')->path($scan->audio_path);

        if (!file_exists($audioPath)) {
            throw new \RuntimeException("File audio tidak ada: {$audioPath}");
        }

        $headers = array_filter(['X-Api-Secret' => $this->secret ?: null]);
        $headers['Accept'] = 'application/json';

        $response = Http::withHeaders($headers)
            ->timeout($this->timeout)
            ->attach('audio', fopen($audioPath, 'r'), basename($audioPath))
            ->post("{$this->baseUrl}/predict", ['scan_id' => $scan->id]);

        if ($response->failed()) {
            $msg = $response->json('detail') ?? $response->body();
            throw new \RuntimeException("AI API [{$response->status()}]: {$msg}");
        }

        return $response->json();
    }

    /**
     * Proses response dari Python — handle quality_error dan hasil normal.
     */
    private function handleResponse(AiScan $scan, array $data): void
    {
        // ── Langkah 1: Audio kualitas buruk ──────────────────────────────────
        if (!($data['success'] ?? false) && isset($data['quality_error'])) {
            $scan->update([
                'status'          => AiScanStatus::Failed,
                'recommendations' => [$data['quality_error']],
                'quality_warning' => $data['quality_error'],
            ]);
            Log::warning("AiScan #{$scan->id}: kualitas audio buruk — {$data['quality_error']}");
            return;
        }

        if (!($data['success'] ?? false)) {
            throw new \RuntimeException("AI API mengembalikan success=false: " . json_encode($data));
        }

        // ── Langkah 3: Simpan hasil diagnostik ───────────────────────────────
        $result = $data['result'];
        $this->saveResult($scan, $result);
    }

    /**
     * Simpan semua field diagnostik ke database.
     */
    private function saveResult(AiScan $scan, array $r): void
    {
        $confidence = (float) ($r['confidence'] ?? 0);
        $needsReview = (bool) ($r['needs_manual_review'] ?? false);

        // Download spectrogram
        $spectrogramPath = null;
        if (!empty($r['spectrogram_path'])) {
            $spectrogramPath = $this->downloadSpectrogram($r['spectrogram_path'], $scan->id);
        }

        // Status selalu completed setelah AI selesai
        // Mekanik bisa tambahkan review kapan saja (opsional)
        $status = AiScanStatus::Completed;

        $scan->update([
            'status'             => $status,
            // Identifikasi
            'predicted_class'    => $r['predicted_class'] ?? null,
            'class_label'        => $r['class_label'] ?? null,
            'confidence'         => $confidence,
            // Urgensi
            'urgency_level'      => $r['urgency']['level'] ?? null,
            // Gelombang
            'wave_analysis'      => $r['wave_analysis'] ?? null,
            // Kualitas
            'quality_score'      => $r['audio_quality']['quality_score'] ?? null,
            'quality_warning'    => $r['audio_quality']['warning'] ?? null,
            // Rekomendasi
            'recommendations'    => $r['recommendations'] ?? [],
            'mechanic_ai_notes'  => $r['mechanic_notes'] ?? null,
            // Spectrogram
            'spectrogram_path'   => $spectrogramPath,
        ]);

        Log::info("AiScan #{$scan->id} selesai", [
            'class'      => $r['predicted_class'] ?? '-',
            'urgency'    => $r['urgency']['level'] ?? '-',
            'confidence' => $confidence,
            'status'     => $status->value,
            'backend'    => $r['backend'] ?? '?',
        ]);
    }

    /**
     * Download spectrogram dari Python dan simpan ke Laravel storage.
     */
    private function downloadSpectrogram(string $remotePath, int $scanId): ?string
    {
        try {
            $filename = basename($remotePath);
            $headers  = array_filter(['X-Api-Secret' => $this->secret ?: null]);
            $response = Http::withHeaders($headers)->timeout(30)
                ->get("{$this->baseUrl}/spectrogram/{$filename}");

            if ($response->ok()) {
                $local = "ai-scans/spectrograms/scan_{$scanId}_{$filename}";
                Storage::disk('public')->put($local, $response->body());
                return $local;
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal download spectrogram scan #{$scanId}: " . $e->getMessage());
        }
        return null;
    }

    public function healthCheck(): array
    {
        try {
            return Http::timeout(5)->get("{$this->baseUrl}/health")->json() ?? ['status' => 'error'];
        } catch (\Throwable $e) {
            return ['status' => 'unreachable', 'error' => $e->getMessage()];
        }
    }
}
