<?php


declare(strict_types=1);


namespace App\Services\Pdf;


use App\Models\SalesOrder;
use Barryvdh\DomPDF\Facade\Pdf;


class ContractPdfService
{
    public function generate(SalesOrder $salesOrder): \Barryvdh\DomPDF\PDF
    {
        $salesOrder->load(['customer.user', 'items.carUnit']);


        $pdf = Pdf::loadView('pdf.contract', [
            'salesOrder' => $salesOrder,
            'customer' => $salesOrder->customer,
            'carUnit' => $salesOrder->items->first()?->carUnit,
            'generatedAt' => now(),
        ]);


        $pdf->setPaper('A4', 'portrait');


        return $pdf;
    }


    public function download(SalesOrder $salesOrder): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = $this->generate($salesOrder);


        return $pdf->download("kontrak-{$salesOrder->code}.pdf");
    }


    public function stream(SalesOrder $salesOrder): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generate($salesOrder);


        return $pdf->stream("kontrak-{$salesOrder->code}.pdf");
    }
}



