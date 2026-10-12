<?php


declare(strict_types=1);


namespace App\Services\Pdf;


use App\Models\SalesOrder;
use Barryvdh\DomPDF\Facade\Pdf;


class PosReceiptPdfService
{
    public function generate(SalesOrder $salesOrder): \Barryvdh\DomPDF\PDF
    {
        $salesOrder->load(['customer.user', 'items.sparePart', 'payments']);


        $pdf = Pdf::loadView('pdf.pos-receipt', [
            'salesOrder' => $salesOrder,
            'customer' => $salesOrder->customer,
            'items' => $salesOrder->items,
            'payments' => $salesOrder->payments,
            'generatedAt' => now(),
        ]);


        // POS receipt typically uses thermal printer size (80mm)
        $pdf->setPaper([0, 0, 226.77, 567], 'portrait'); // 80mm x 200mm


        return $pdf;
    }


    public function download(SalesOrder $salesOrder): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = $this->generate($salesOrder);


        return $pdf->download("pos-receipt-{$salesOrder->code}.pdf");
    }


    public function stream(SalesOrder $salesOrder): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generate($salesOrder);


        return $pdf->stream("pos-receipt-{$salesOrder->code}.pdf");
    }
}



