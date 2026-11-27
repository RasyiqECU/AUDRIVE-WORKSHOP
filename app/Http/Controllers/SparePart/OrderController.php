<?php

declare(strict_types=1);

namespace App\Http\Controllers\SparePart;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user()?->customer;

        // Tanpa profil customer tidak ada pesanan milik sendiri (jangan tampilkan pesanan walk-in)
        $orders = SalesOrder::where('customer_id', $customer?->id ?? 0)
            ->with(['items.sparePart'])
            ->latest()
            ->paginate(10);

        return view('spare-parts.orders.index', compact('orders'));
    }

    public function show(Request $request, SalesOrder $order): View
    {
        $this->authorizeOrder($request, $order);

        $order->load(['items.sparePart', 'customer']);

        return view('spare-parts.orders.show', compact('order'));
    }

    public function invoice(Request $request, SalesOrder $order): Response|RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        $order->load(['items.sparePart', 'customer']);
        $invoice = app(InvoiceService::class)->ensureInvoiceForSalesOrder($order);

        if ($request->boolean('download')) {
            return app(InvoiceService::class)->renderPdf($invoice, stream: false);
        }

        return redirect()->route('customer.invoices.show', $invoice);
    }

    private function authorizeOrder(Request $request, SalesOrder $order): void
    {
        $user = $request->user();

        abort_unless(
            $user->role === 'super-admin' || ($user->customer && $order->customer_id === $user->customer->id),
            403
        );
    }
}
