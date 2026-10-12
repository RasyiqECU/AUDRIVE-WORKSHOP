<?php


namespace App\Jobs;


use App\Models\AiScan;
use App\Services\Ai\AiScanServiceManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class ProcessAiScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;


    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 30;


    /**
     * Create a new job instance.
     */
    public function __construct(
        public AiScan $scan
    ) {}


    /**
     * Execute the job.
     */
    public function handle(AiScanServiceManager $manager): void
    {
        Log::info('Processing AI scan', ['scan_id' => $this->scan->id]);


        try {
            $service = $manager->driver();
            $service->processScan($this->scan);


            Log::info('AI scan processed successfully', [
                'scan_id' => $this->scan->id,
                'status' => $this->scan->status,
            ]);


        } catch (\Exception $e) {
            Log::error('Failed to process AI scan', [
                'scan_id' => $this->scan->id,
                'error' => $e->getMessage(),
            ]);


            // Mark as failed for manual review
            $this->scan->update([
                'status' => 'failed',
                'recommendations' => array_merge(
                    $this->scan->recommendations ?? [],
                    ['Processing failed: ' . $e->getMessage()]
                ),
            ]);


            throw $e; // Re-throw to trigger retry
        }
    }


    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception): void
    {
        Log::error('AI scan job failed permanently', [
            'scan_id' => $this->scan->id,
            'error' => $exception->getMessage(),
        ]);


        // Mark as manual_review for human intervention
        $this->scan->update([
            'status' => 'manual_review',
            'recommendations' => array_merge(
                $this->scan->recommendations ?? [],
                ['AI processing failed. Manual review required.']
            ),
        ]);
    }
}



