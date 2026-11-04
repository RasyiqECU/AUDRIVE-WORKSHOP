<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Pastikan setiap SalesOrder memiliki relasi record Invoice.
     */
    public function ensureInvoiceForSalesOrder(SalesOrder $salesOrder): Invoice
    {
        $existing = Invoice::where('invoiceable_type', SalesOrder::class)
            ->where('invoiceable_id', $salesOrder->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $typePrefix = match ($salesOrder->type) {
            SalesOrderType::CarSales => 'INV-CAR-',
            SalesOrderType::Service => 'INV-WO-',
            SalesOrderType::SparePart => 'INV-PART-',
            default => 'INV-',
        };

        $status = in_array($salesOrder->status, [SalesOrderStatus::Paid, SalesOrderStatus::Completed], true) ? 'paid' : 'draft';

        return Invoice::create([
            'invoiceable_type' => SalesOrder::class,
            'invoiceable_id' => $salesOrder->id,
            'number' => $typePrefix.date('Ymd').'-'.strtoupper(Str::random(6)),
            'status' => $status,
            'due_at' => $salesOrder->created_at->addDays(7),
            'total_idr' => $salesOrder->total_idr,
            'notes' => $salesOrder->notes,
        ]);
    }

    /**
     * Sinkronisasi semua SalesOrder yang belum punya Invoice (Self-healing).
     */
    public function syncMissingInvoices(): void
    {
        $unlinkedOrders = SalesOrder::whereDoesntHave('invoice')->get();
        foreach ($unlinkedOrders as $order) {
            $this->ensureInvoiceForSalesOrder($order);
        }
    }

    /**
     * Dapatkan riwayat invoice untuk portal pelanggan.
     */
    public function getCustomerInvoices(
        Customer $customer,
        ?string $status = null,
        ?string $type = null,
        ?string $search = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        $this->syncMissingInvoices();

        $query = Invoice::where('invoiceable_type', SalesOrder::class)
            ->whereHasMorph('invoiceable', [SalesOrder::class], function ($q) use ($customer, $type) {
                $q->where('customer_id', $customer->id);
                if ($type && $type !== 'all') {
                    $q->where('type', $type);
                }
            })
            ->with(['invoiceable.customer.user', 'invoiceable.payments', 'invoiceable.items'])
            ->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhereHasMorph('invoiceable', [SalesOrder::class], function ($sub) use ($search) {
                        $sub->where('code', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Dapatkan daftar invoice untuk Admin / Kasir / Super Admin.
     */
    public function getAdminInvoices(
        ?string $status = null,
        ?string $type = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $search = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $this->syncMissingInvoices();

        $query = Invoice::where('invoiceable_type', SalesOrder::class)
            ->with(['invoiceable.customer.user', 'invoiceable.payments', 'invoiceable.items', 'invoiceable.creator'])
            ->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($type && $type !== 'all') {
            $query->whereHasMorph('invoiceable', [SalesOrder::class], function ($q) use ($type) {
                $q->where('type', $type);
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhereHasMorph('invoiceable', [SalesOrder::class], function ($sub) use ($search) {
                        $sub->where('code', 'like', "%{$search}%")
                            ->orWhereHas('customer.user', function ($u) use ($search) {
                                $u->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            });
                    });
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Dapatkan ringkasan statistik invoice untuk dashboard admin.
     */
    public function getInvoiceStats(): array
    {
        $this->syncMissingInvoices();

        $totalInvoices = Invoice::count();
        $totalPaid = Invoice::where('status', 'paid')->sum('total_idr');
        $totalPending = Invoice::where('status', '!=', 'paid')->sum('total_idr');
        $countPaid = Invoice::where('status', 'paid')->count();
        $countPending = Invoice::where('status', '!=', 'paid')->count();

        return [
            'total_invoices' => $totalInvoices,
            'total_paid' => (int) $totalPaid,
            'total_pending' => (int) $totalPending,
            'count_paid' => $countPaid,
            'count_pending' => $countPending,
        ];
    }

    /**
     * Generate PDF stream/download for an invoice.
     */
    public function renderPdf(Invoice $invoice, bool $stream = false): Response
    {
        /** @var SalesOrder $salesOrder */
        $salesOrder = $invoice->invoiceable;
        $salesOrder->load(['items.sparePart', 'customer.user', 'payments', 'creator']);

        $customer = $salesOrder->customer;
        $items = $salesOrder->items;
        $payments = $salesOrder->payments;

        if ($salesOrder->type === SalesOrderType::Service) {
            $workOrder = null;
            if (preg_match('/#?(WO-[A-Za-z0-9-]+)/', $salesOrder->notes ?? '', $matches)) {
                $workOrder = WorkOrder::where('code', $matches[1])
                    ->with(['vehicle', 'mechanic', 'items.sparePart'])
                    ->first();
            }

            $pdf = Pdf::loadView('pdf.service-invoice', compact('salesOrder', 'invoice', 'workOrder', 'customer', 'items', 'payments'));
        } else {
            $carItem = $items->firstWhere('car_unit_id');
            $carUnit = $carItem?->carUnit ?? ($salesOrder->carUnit ?? null);

            $pdf = Pdf::loadView('pdf.invoice', compact('salesOrder', 'invoice', 'customer', 'items', 'payments', 'carUnit'));
        }

        $pdf->setPaper('a4', 'portrait');

        $fileName = "Invoice-{$invoice->number}.pdf";

        return $stream ? $pdf->stream($fileName) : $pdf->download($fileName);
    }
}
