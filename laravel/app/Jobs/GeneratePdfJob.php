<?php


namespace App\Jobs;


use App\Models\SalesOrder;
use App\Services\Pdf\InvoicePdfService;
use App\Services\Pdf\ContractPdfService;
use App\Services\Pdf\ServiceInvoicePdfService;
use App\Services\Pdf\ServiceReceiptPdfService;
use App\Services\Pdf\PosReceiptPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 60;


    public function __construct(
        protected string $type,
        protected int $modelId,
        protected string $modelType
    ) {}


    public function handle(): void
    {
        try {
            $model = $this->getModel();


            if (!$model) {
                throw new \Exception("Model not found: {$this->modelType}#{$this->modelId}");
            }


            $pdfService = $this->getPdfService();
            $pdf = $pdfService->generate($model);


            $filename = $this->generateFilename();
            $path = "pdfs/{$filename}";


            Storage::disk('public')->put($path, $pdf->output());


            // Update model with PDF path
            $model->update(['pdf_path' => $path]);


            Log::info("PDF generated successfully", [
                'type' => $this->type,
                'model_id' => $this->modelId,
                'path' => $path,
            ]);


        } catch (\Exception $e) {
            Log::error("PDF generation failed", [
                'type' => $this->type,
                'model_id' => $this->modelId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }


    protected function getModel()
    {
        return match ($this->modelType) {
            SalesOrder::class => SalesOrder::find($this->modelId),
            default => null,
        };
    }


    protected function getPdfService()
    {
        return match ($this->type) {
            'car_invoice' => app(InvoicePdfService::class),
            'car_contract' => app(ContractPdfService::class),
            'service_invoice' => app(ServiceInvoicePdfService::class),
            'service_receipt' => app(ServiceReceiptPdfService::class),
            'pos_receipt' => app(PosReceiptPdfService::class),
            default => throw new \Exception("Unknown PDF type: {$this->type}"),
        };
    }


    protected function generateFilename(): string
    {
        $timestamp = now()->format('Ymd_His');
        return "{$this->type}_{$this->modelId}_{$timestamp}.pdf";
    }
}



