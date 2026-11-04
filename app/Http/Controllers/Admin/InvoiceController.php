<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    /**
     * Tampilkan daftar semua invoice transaksi (Super Admin, Manager, Kasir, Sales).
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');
        $type = $request->input('type', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        $invoices = $this->invoiceService->getAdminInvoices(
            $status,
            $type,
            $dateFrom,
            $dateTo,
            $search,
            15
        );

        $stats = $this->invoiceService->getInvoiceStats();

        return view('admin.invoices.index', compact(
            'invoices',
            'stats',
            'status',
            'type',
            'dateFrom',
            'dateTo',
            'search'
        ));
    }

    /**
     * Tampilkan detail invoice & bukti pembayaran verifikasi.
     */
    public function show(Invoice $invoice): View
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = $invoice->invoiceable;
        $salesOrder->load(['items.sparePart', 'customer.user', 'payments', 'creator']);

        return view('admin.invoices.show', compact('invoice', 'salesOrder'));
    }

    /**
     * Download dokumen PDF resmi invoice & bukti pembayaran.
     */
    public function download(Invoice $invoice): Response
    {
        return $this->invoiceService->renderPdf($invoice, stream: false);
    }

    /**
     * Cetak / Stream view PDF di browser.
     */
    public function print(Invoice $invoice): Response
    {
        return $this->invoiceService->renderPdf($invoice, stream: true);
    }
}
