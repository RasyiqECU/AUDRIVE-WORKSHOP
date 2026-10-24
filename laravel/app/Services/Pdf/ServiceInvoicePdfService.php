<?php


declare(strict_types=1);


namespace App\Services\Pdf;


use App\Models\SalesOrder;
use Barryvdh\DomPDF\Facade\Pdf;


class ServiceInvoicePdfService
{
    public function generate(SalesOrder $salesOrder): \Barryvdh\DomPDF\PDF
    {
        $salesOrder->load(['customer.user', 'items.sparePart', 'workOrder']);


        $pdf = Pdf::loadView('pdf.service-invoice', [
            'salesOrder' => $salesOrder,
            'customer' => $salesOrder->customer,
            'items' => $salesOrder->items,
            'workOrder' => $salesOrder->workOrder,
            'generatedAt' => now(),
        ]);


        $pdf->setPaper('A4', 'portrait');


        return $pdf;
    }


    public function download(SalesOrder $salesOrder): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = $this->generate($salesOrder);


        return $pdf->download("service-invoice-{$salesOrder->code}.pdf");
    }


    public function stream(SalesOrder $salesOrder): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generate($salesOrder);


        return $pdf->stream("service-invoice-{$salesOrder->code}.pdf");
    }
}



