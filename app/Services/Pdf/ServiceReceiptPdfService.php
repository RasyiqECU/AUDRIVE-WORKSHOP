<?php

declare(strict_types=1);

namespace App\Services\Pdf;

use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceReceiptPdfService
{
    public function generate(WorkOrder $workOrder): \Barryvdh\DomPDF\PDF
    {
        $workOrder->load(['vehicle.customer.user', 'items.sparePart', 'mechanic']);

        $pdf = Pdf::loadView('pdf.service-receipt', [
            'workOrder' => $workOrder,
            'customer' => $workOrder->vehicle->customer,
            'vehicle' => $workOrder->vehicle,
            'items' => $workOrder->billableItems()->with('sparePart')->get(),
            'mechanic' => $workOrder->mechanic,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    public function download(WorkOrder $workOrder): StreamedResponse
    {
        $pdf = $this->generate($workOrder);

        return $pdf->download("service-receipt-{$workOrder->code}.pdf");
    }

    public function stream(WorkOrder $workOrder): Response
    {
        $pdf = $this->generate($workOrder);

        return $pdf->stream("service-receipt-{$workOrder->code}.pdf");
    }
}
