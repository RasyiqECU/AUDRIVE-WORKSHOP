<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

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
     * Tampilkan riwayat semua tagihan & invoice customer.
     */
    public function index(Request $request): View
    {
        $customer = $request->user()->customer;
        if (! $customer) {
            abort(403, 'Akun Anda belum terdaftar sebagai data customer.');
        }

        $status = $request->input('status', 'all');
        $type = $request->input('type', 'all');
        $search = $request->input('search');

        $invoices = $this->invoiceService->getCustomerInvoices($customer, $status, $type, $search, 10);

        // Ringkasan metrik customer
        $totalPaid = Invoice::where('invoiceable_type', SalesOrder::class)
            ->where('status', 'paid')
            ->whereHasMorph('invoiceable', [SalesOrder::class], fn ($q) => $q->where('customer_id', $customer->id))
            ->sum('total_idr');

        $totalPending = Invoice::where('invoiceable_type', SalesOrder::class)
            ->where('status', '!=', 'paid')
            ->whereHasMorph('invoiceable', [SalesOrder::class], fn ($q) => $q->where('customer_id', $customer->id))
            ->sum('total_idr');

        $countPaid = Invoice::where('invoiceable_type', SalesOrder::class)
            ->where('status', 'paid')
            ->whereHasMorph('invoiceable', [SalesOrder::class], fn ($q) => $q->where('customer_id', $customer->id))
            ->count();

        return view('customer.invoices.index', compact(
            'invoices',
            'status',
            'type',
            'search',
            'totalPaid',
            'totalPending',
            'countPaid'
        ));
    }

    /**
     * Tampilkan detail invoice & bukti pembayaran interaktif di layar.
     */
    public function show(Request $request, Invoice $invoice): View
    {
        $customer = $request->user()->customer;
        /** @var SalesOrder $salesOrder */
        $salesOrder = $invoice->invoiceable;

        if (! $customer || $salesOrder->customer_id !== $customer->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat invoice ini.');
        }

        $salesOrder->load(['items.sparePart', 'customer.user', 'payments', 'creator']);

        return view('customer.invoices.show', compact('invoice', 'salesOrder'));
    }

    /**
     * Download dokumen PDF resmi invoice & bukti pembayaran.
     */
    public function download(Request $request, Invoice $invoice): Response
    {
        $customer = $request->user()->customer;
        /** @var SalesOrder $salesOrder */
        $salesOrder = $invoice->invoiceable;

        if (! $customer || $salesOrder->customer_id !== $customer->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh invoice ini.');
        }

        return $this->invoiceService->renderPdf($invoice, stream: false);
    }

    /**
     * Cetak langsung / Stream view PDF di browser.
     */
    public function print(Request $request, Invoice $invoice): Response
    {
        $customer = $request->user()->customer;
        /** @var SalesOrder $salesOrder */
        $salesOrder = $invoice->invoiceable;

        if (! $customer || $salesOrder->customer_id !== $customer->id) {
            abort(403, 'Akses ditolak.');
        }

        return $this->invoiceService->renderPdf($invoice, stream: true);
    }
}
