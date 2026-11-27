<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CarUnitStatus;
use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Enums\WorkOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\WorkOrder;
use App\Services\CarBookingService;
use App\Services\CheckoutService;
use App\Services\PosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        protected PosService $posService
    ) {}

    public function index(Request $request): View
    {
        $parts = SparePart::where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->get();

        // Cari kode WO yang sudah dibayar
        $paidWoCodes = SalesOrder::where('type', SalesOrderType::Service)
            ->where('status', SalesOrderStatus::Paid)
            ->pluck('notes')
            ->map(function ($notes) {
                preg_match('/WO-[A-Z0-9]+|WO\d+/', (string) $notes, $matches);

                return $matches[0] ?? null;
            })
            ->filter()
            ->toArray();

        // Ambil work order yang aktif / selesai dan belum dibayar
        $pendingWorkOrders = WorkOrder::where('status', WorkOrderStatus::Completed)
            ->whereNull('paid_at')
            ->whereNotIn('code', $paidWoCodes)
            ->where(fn ($q) => $q->where('total_idr', '>', 0)->orWhere('is_warranty', true))
            ->with(['vehicle.customer.user', 'items.sparePart', 'mechanic', 'salesOrder'])
            ->latest()
            ->get();

        $pendingCarOrders = SalesOrder::where('type', SalesOrderType::CarSales)
            ->where('status', SalesOrderStatus::Pending)
            ->with(['customer.user', 'items'])
            ->latest()
            ->get();

        $pendingPartOrders = SalesOrder::where('type', SalesOrderType::SparePart)
            ->where('status', SalesOrderStatus::Pending)
            ->where(fn ($q) => $q->whereNull('payment_channel')->orWhere('payment_channel', 'cashier'))
            ->with(['customer.user', 'items'])
            ->latest()
            ->get();

        $recentTransactions = SalesOrder::whereIn('type', [SalesOrderType::Pos, SalesOrderType::Service, SalesOrderType::CarSales, SalesOrderType::SparePart])
            ->where('status', SalesOrderStatus::Paid)
            ->with('customer.user')
            ->latest()
            ->limit(10)
            ->get();

        $consignmentPayouts = CarUnit::where('ownership', 'consignment')
            ->where('status', CarUnitStatus::Sold)
            ->whereNull('seller_paid_at')
            ->with('seller.user')
            ->get();

        $selectedWoId = $request->query('work_order_id');
        $selectedWorkOrder = $selectedWoId ? WorkOrder::with(['vehicle.customer.user', 'items.sparePart', 'mechanic'])->find($selectedWoId) : null;

        $selectedCarOrderId = $request->query('car_sales_id');
        $selectedCarOrder = $selectedCarOrderId ? SalesOrder::with(['customer.user', 'items'])->find($selectedCarOrderId) : null;

        return view('admin.pos.index', compact('parts', 'pendingWorkOrders', 'pendingCarOrders', 'pendingPartOrders', 'consignmentPayouts', 'recentTransactions', 'selectedWorkOrder', 'selectedCarOrder'));
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'required|string|min:2',
        ]);

        $parts = SparePart::where('status', 'active')
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            })
            ->with('category')
            ->limit(20)
            ->get();

        return response()->json($parts);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.spare_part_id' => 'required|exists:spare_parts,id',
            'items.*.qty' => 'required|integer|min:1',
            'customer_id' => 'nullable|exists:customers,id',
            'discount_idr' => $this->discountRule($request),
            'payment_method' => 'required|in:cash,card,transfer,ewallet',
        ]);

        $customer = $request->customer_id ? Customer::find($request->customer_id) : null;

        try {
            $salesOrder = $this->posService->createPosTransaction(
                $request->items,
                $customer,
                $request->input('discount_idr', 0),
                $request->payment_method,
                auth()->id()
            );

            return redirect()
                ->route('admin.pos.receipt', $salesOrder)
                ->with('success', 'Transaksi POS berhasil.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function payWorkOrder(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $validated = $request->validate([
            'discount_idr' => $this->discountRule($request),
            'payment_method' => 'required|in:cash,card,transfer,ewallet',
        ]);

        try {
            $salesOrder = $this->posService->createWorkOrderPayment(
                $workOrder,
                (int) ($validated['discount_idr'] ?? 0),
                (string) $validated['payment_method'],
                auth()->id()
            );

            return redirect()
                ->route('admin.pos.receipt', $salesOrder)
                ->with('success', "Pembayaran Work Order #{$workOrder->code} berhasil diproses.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function payCarSales(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        $validated = $request->validate([
            'discount_idr' => $this->discountRule($request),
            'payment_method' => 'required|in:cash,card,transfer,ewallet',
        ]);

        try {
            $paidOrder = $this->posService->payCarSalesOrder(
                $salesOrder,
                (int) ($validated['discount_idr'] ?? 0),
                (string) $validated['payment_method'],
                auth()->id()
            );

            return redirect()
                ->route('admin.pos.receipt', $paidOrder)
                ->with('success', "Pembayaran Pembelian Mobil #{$paidOrder->code} berhasil diproses.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /** Pelunasan pesanan spare part online yang masih menunggu pembayaran. */
    public function payPartOrder(Request $request, SalesOrder $salesOrder, CheckoutService $checkoutService): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,transfer,ewallet',
        ]);

        abort_unless($salesOrder->type === SalesOrderType::SparePart, 404);

        try {
            $checkoutService->processPayment($salesOrder, $validated['payment_method'], $salesOrder->total_idr);

            return redirect()
                ->route('admin.pos.receipt', $salesOrder)
                ->with('success', "Pembayaran pesanan #{$salesOrder->code} berhasil diproses.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /** Terima booking fee mobil di kasir; unit langsung terkunci. */
    public function takeDeposit(Request $request, SalesOrder $salesOrder, CarBookingService $bookings): RedirectResponse
    {
        $validated = $request->validate(['payment_method' => 'required|in:cash,card,transfer,ewallet']);

        try {
            DB::transaction(function () use ($salesOrder, $validated, $bookings) {
                $order = SalesOrder::whereKey($salesOrder->id)->lockForUpdate()->firstOrFail();

                if ($order->status !== SalesOrderStatus::Pending || $order->deposit_idr <= 0 || $order->paid_idr > 0) {
                    throw new \InvalidArgumentException('Pesanan ini tidak menunggu booking fee.');
                }

                Payment::create([
                    'paymentable_type' => SalesOrder::class,
                    'paymentable_id' => $order->id,
                    'method' => $validated['payment_method'],
                    'purpose' => 'booking_fee',
                    'status' => PaymentStatus::Success,
                    'amount_idr' => $order->deposit_idr,
                    'reference' => 'DP-'.strtoupper(Str::random(10)),
                    'paid_at' => now(),
                ]);
                $order->increment('paid_idr', $order->deposit_idr);
                $bookings->recordDeposit($order);
            });
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Booking fee diterima. Unit terkunci.');
    }

    /** Diskon khusus wajib disetujui Manager; hanya Super Admin yang bisa mengisi langsung. */
    private function discountRule(Request $request): array
    {
        $max = $request->user()->role === 'super-admin' ? [] : ['max:0'];

        return ['nullable', 'integer', 'min:0', ...$max];
    }

    public function receipt(SalesOrder $salesOrder): View
    {
        $salesOrder->load(['items.sparePart', 'customer.user', 'payments']);

        return view('admin.pos.receipt', compact('salesOrder'));
    }

    public function printReceipt(SalesOrder $salesOrder): View
    {
        $salesOrder->load(['items.sparePart', 'customer.user', 'payments']);

        return view('admin.pos.print-receipt', compact('salesOrder'));
    }
}
