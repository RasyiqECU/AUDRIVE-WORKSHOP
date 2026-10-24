<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Enums\WorkOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\WorkOrder;
use App\Services\PosService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $pendingWorkOrders = WorkOrder::whereIn('status', [
                WorkOrderStatus::Completed,
                WorkOrderStatus::InProgress,
                WorkOrderStatus::QualityControl,
            ])
            ->whereNotIn('code', $paidWoCodes)
            ->where('total_idr', '>', 0)
            ->with(['vehicle.customer.user', 'items.sparePart', 'mechanic'])
            ->latest()
            ->get();

        $pendingCarOrders = SalesOrder::where('type', SalesOrderType::CarSales)
            ->where('status', SalesOrderStatus::Pending)
            ->with(['customer.user', 'items'])
            ->latest()
            ->get();

        $recentTransactions = SalesOrder::whereIn('type', [SalesOrderType::Pos, SalesOrderType::Service, SalesOrderType::CarSales])
            ->with('customer.user')
            ->latest()
            ->limit(10)
            ->get();

        $selectedWoId = $request->query('work_order_id');
        $selectedWorkOrder = $selectedWoId ? WorkOrder::with(['vehicle.customer.user', 'items.sparePart', 'mechanic'])->find($selectedWoId) : null;

        $selectedCarOrderId = $request->query('car_sales_id');
        $selectedCarOrder = $selectedCarOrderId ? SalesOrder::with(['customer.user', 'items'])->find($selectedCarOrderId) : null;

        return view('admin.pos.index', compact('parts', 'pendingWorkOrders', 'pendingCarOrders', 'recentTransactions', 'selectedWorkOrder', 'selectedCarOrder'));
    }


    public function search(Request $request): \Illuminate\Http\JsonResponse
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
            'discount_idr' => 'nullable|integer|min:0',
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
            'discount_idr' => 'nullable|integer|min:0',
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
            'discount_idr' => 'nullable|integer|min:0',
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



