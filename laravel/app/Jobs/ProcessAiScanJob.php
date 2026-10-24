<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AiScanStatus;
use App\Models\AiScan;
use App\Services\AiScanService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class ProcessAiScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Jumlah percobaan ulang saat gagal. */
    public int $tries = 3;

    /** Timeout per attempt (detik) — harus > AI service timeout. */
    public int $timeout = 120;

    /** Backoff (detik) antar retry: 10s, 30s, 60s */
    public array $backoff = [10, 30, 60];

    public function __construct(public readonly AiScan $scan) {}

    /**
     * Proses scan audio melalui Python AI service.
     */
    public function handle(AiScanService $service): void
    {
        Log::info("ProcessAiScanJob: mulai", ['scan_id' => $this->scan->id]);

        $service->process($this->scan);

        Log::info("ProcessAiScanJob: selesai", [
            'scan_id' => $this->scan->id,
            'status'  => $this->scan->fresh()?->status?->value,
        ]);
    }

    /**
     * Dipanggil setelah semua retry habis.
     */
    public function failed(\Throwable $e): void
    {
        Log::error("ProcessAiScanJob: gagal permanen", [
            'scan_id' => $this->scan->id,
            'error'   => $e->getMessage(),
        ]);

        $this->scan->update([
            'status'          => AiScanStatus::ManualReview,
            'recommendations' => array_merge(
                $this->scan->recommendations ?? [],
                ['AI processing gagal. Silakan hubungi teknisi untuk review manual.']
            ),
        ]);
    }
}
