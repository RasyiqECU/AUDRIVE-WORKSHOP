<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CarUnitStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Enums\WorkOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Resolves date range based on preset or custom input.
     */
    protected function resolveDateRange(Request $request): array
    {
        $preset = $request->input('preset', 'this_month');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        if ($startDateInput || $endDateInput) {
            $startDate = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : null;
            $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : Carbon::now()->endOfDay();
            $preset = 'custom';
        } else {
            switch ($preset) {
                case 'today':
                    $startDate = Carbon::today()->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'this_week':
                    $startDate = Carbon::now()->startOfWeek()->startOfDay();
                    $endDate = Carbon::now()->endOfWeek()->endOfDay();
                    break;
                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth()->startOfDay();
                    $endDate = Carbon::now()->subMonth()->endOfMonth()->endOfDay();
                    break;
                case 'this_year':
                    $startDate = Carbon::now()->startOfYear()->startOfDay();
                    $endDate = Carbon::now()->endOfYear()->endOfDay();
                    break;
                case 'all':
                    $startDate = null;
                    $endDate = null;
                    break;
                case 'this_month':
                default:
                    $preset = 'this_month';
                    $startDate = Carbon::now()->startOfMonth()->startOfDay();
                    $endDate = Carbon::now()->endOfMonth()->endOfDay();
                    break;
            }
        }

        return [$startDate, $endDate, $preset];
    }

    /**
     * Executive Summary & Performance Analytics
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Gudang tanpa omzet; Kasir memakai Laporan Shift (uang yang diterimanya)
        $user = $request->user();
        if (in_array($user->role, ['inventory', 'cashier'], true)) {
            return redirect()->route($user->role === 'inventory' ? 'admin.reports.inventory' : 'admin.reports.finance');
        }
        $fullScope = in_array($user->role, ['super-admin', 'owner', 'manager'], true);

        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);

        // Omzet menurut tanggal lunas; Sales hanya yang ditanganinya
        $salesQuery = SalesOrder::earnedBetween($startDate, $endDate)
            ->when($user->role === 'sales', fn ($q) => $q->where('sales_id', $user->id));
        $woQuery = WorkOrder::query()->when(! $fullScope, fn ($q) => $q->whereRaw('1 = 0'));

        if ($startDate && $endDate) {
            $woQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Total Omzet Terverifikasi (Paid / Completed)
        $totalRevenue = (int) (clone $salesQuery)->sum('revenue_idr');
        $totalDiscounts = (int) (clone $salesQuery)->sum('discount_idr');
        $totalTaxes = (int) (clone $salesQuery)->sum('tax_idr');
        $totalOrdersCount = (clone $salesQuery)->count();

        // Breakdown per Divisi
        $carRevenue = (int) (clone $salesQuery)->where('type', SalesOrderType::CarSales)->sum('revenue_idr');
        $carSalesCount = (clone $salesQuery)->where('type', SalesOrderType::CarSales)->count();

        $workshopRevenue = (int) (clone $salesQuery)->where('type', SalesOrderType::Service)->sum('revenue_idr');
        $completedWorkOrders = (clone $woQuery)->whereIn('status', [WorkOrderStatus::Completed, WorkOrderStatus::Delivered])->count();
        $activeWorkOrders = (clone $woQuery)->whereIn('status', [WorkOrderStatus::Queued, WorkOrderStatus::InProgress, WorkOrderStatus::WaitingPartConfirmation])->count();

        $sparePartRevenue = (int) (clone $salesQuery)->whereIn('type', [SalesOrderType::SparePart, SalesOrderType::Pos])->sum('revenue_idr');

        $totalCustomers = $fullScope ? Customer::count() : 0;

        // Recent Verified Sales Transactions
        $recentSales = (clone $salesQuery)->with(['customer.user', 'creator'])
            ->latest('paid_at')
            ->take(8)
            ->get();

        // Recent Work Orders
        $recentWorkOrders = (clone $woQuery)->with(['vehicle.customer.user', 'mechanic'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.reports.index', compact(
            'totalRevenue',
            'totalDiscounts',
            'totalTaxes',
            'totalOrdersCount',
            'carRevenue',
            'carSalesCount',
            'workshopRevenue',
            'completedWorkOrders',
            'activeWorkOrders',
            'sparePartRevenue',
            'totalCustomers',
            'recentSales',
            'recentWorkOrders',
            'startDate',
            'endDate',
            'preset',
            'fullScope'
        ));
    }

    /**
     * Laporan Resmi Penjualan Supercar
     */
    public function sales(Request $request): View
    {
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $query = SalesOrder::where('type', SalesOrderType::CarSales)
            ->with(['customer.user', 'items.carUnit', 'sales', 'payments'])
            ->inPeriod($startDate, $endDate);

        // Sales hanya melihat penjualan yang ditanganinya
        $user = $request->user();
        if ($user && $user->role === 'sales') {
            $query->where('sales_id', $user->id);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('customer.user', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Metrics for the filtered query
        $totalCarRevenue = (int) (clone $query)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('revenue_idr');
        $totalDiscountGiven = (int) (clone $query)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('discount_idr');
        $unitsSold = (clone $query)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->count();
        $averageSellingPrice = (int) (clone $query)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->avg('total_idr');
        $salesRecap = $this->salesRecap($startDate, $endDate, $user);
        $availableUnits = CarUnit::where('status', CarUnitStatus::Available)->count();

        // Paginate for web, or fetch all if in print mode
        $isPrint = $request->boolean('print');
        $query->orderByRaw('COALESCE(paid_at, created_at) desc');
        $salesOrders = $isPrint ? (clone $query)->get() : (clone $query)->paginate(15);

        return view('admin.reports.sales', compact(
            'salesOrders',
            'salesRecap',
            'totalCarRevenue',
            'totalDiscountGiven',
            'unitsSold',
            'averageSellingPrice',
            'availableUnits',
            'startDate',
            'endDate',
            'preset',
            'status',
            'search',
            'isPrint'
        ));
    }

    /**
     * Omzet semua jenis transaksi per Sales. Tanpa Sales: suku cadang = mandiri/online,
     * mobil dan servis = belum ada yang menangani.
     */
    protected function salesRecap(?Carbon $start, ?Carbon $end, User $viewer): Collection
    {
        return SalesOrder::earnedBetween($start, $end)
            ->when($viewer->role === 'sales', fn ($q) => $q->where('sales_id', $viewer->id))
            ->with('sales')
            ->get()
            ->groupBy(fn (SalesOrder $order) => $order->sales?->name ?? (
                in_array($order->type, [SalesOrderType::CarSales, SalesOrderType::Service], true)
                    ? 'Belum Ditangani Sales'
                    : 'Penjualan Mandiri / Online'
            ))
            ->map(fn (Collection $orders, string $name) => [
                'name' => $name,
                'count' => $orders->count(),
                'car' => (int) $orders->where('type', SalesOrderType::CarSales)->sum('revenue_idr'),
                'service' => (int) $orders->where('type', SalesOrderType::Service)->sum('revenue_idr'),
                'part' => (int) $orders->whereIn('type', [SalesOrderType::SparePart, SalesOrderType::Pos])->sum('revenue_idr'),
                'total' => (int) $orders->sum('revenue_idr'),
            ])
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Laporan Resmi Workshop & Servis Kendaraan
     */
    public function workshop(Request $request): View
    {
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);
        $status = $request->input('status', 'all');
        $mechanicId = $request->input('mechanic_id');
        $search = $request->input('search');

        $query = WorkOrder::with(['vehicle.customer.user', 'mechanic', 'items.sparePart']);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($mechanicId && $mechanicId !== 'all') {
            $query->where('assigned_mechanic_id', (int) $mechanicId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('vehicle', function ($v) use ($search) {
                        $v->where('plate_number', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vehicle.customer.user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Omzet bengkel = tagihan servis yang lunas pada periode ini
        $paidServices = SalesOrder::earnedBetween($startDate, $endDate)->where('type', SalesOrderType::Service);
        if (($mechanicId && $mechanicId !== 'all') || $search) {
            $paidServices->whereIn('work_order_id', (clone $query)->withoutEagerLoads()->pluck('id'));
        }
        $billed = WorkOrder::whereIn('id', (clone $paidServices)->pluck('work_order_id'));

        $finished = [WorkOrderStatus::Completed, WorkOrderStatus::Delivered];
        $completedRepairs = (clone $query)->whereIn('status', $finished)->count();
        $activeRepairs = (clone $query)->whereIn('status', [WorkOrderStatus::Queued, WorkOrderStatus::InProgress, WorkOrderStatus::WaitingPartConfirmation])->count();
        $totalWorkshopRevenue = (int) (clone $paidServices)->sum('revenue_idr');
        $totalJasaRevenue = (int) (clone $billed)->sum('total_jasa_idr');
        $totalPartRevenue = (int) (clone $billed)->sum('total_part_idr');
        $avgRepairCost = (int) (clone $paidServices)->avg('revenue_idr');

        $mechanics = User::where('role', 'mechanic')->get();

        $isPrint = $request->boolean('print');
        $workOrders = $isPrint ? (clone $query)->latest()->get() : (clone $query)->latest()->paginate(15);

        return view('admin.reports.workshop', compact(
            'workOrders',
            'totalWorkshopRevenue',
            'totalJasaRevenue',
            'totalPartRevenue',
            'completedRepairs',
            'activeRepairs',
            'avgRepairCost',
            'mechanics',
            'startDate',
            'endDate',
            'preset',
            'status',
            'mechanicId',
            'search',
            'isPrint'
        ));
    }

    /**
     * Laporan Resmi Stok & Valuasi Inventori
     */
    public function inventory(Request $request): View
    {
        $categoryId = $request->input('category_id', 'all');
        $stockStatus = $request->input('stock_status', 'all');
        $search = $request->input('search');

        $query = SparePart::with('category');

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', (int) $categoryId);
        }

        if ($stockStatus === 'low_stock') {
            $query->whereColumn('stock', '<=', 'low_stock_threshold');
        } elseif ($stockStatus === 'out_of_stock') {
            $query->where('stock', '<=', 0);
        } elseif ($stockStatus === 'in_stock') {
            $query->whereColumn('stock', '>', 'low_stock_threshold');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Valuasi & Statistik Inventori
        $totalPartsValue = (int) SparePart::sum(DB::raw('stock * price_idr'));
        $totalFilteredValue = (int) (clone $query)->sum(DB::raw('stock * price_idr'));
        $totalItems = SparePart::count();
        $totalStockUnits = (int) SparePart::sum('stock');
        $lowStockCount = SparePart::whereColumn('stock', '<=', 'low_stock_threshold')->count();
        $outOfStockCount = SparePart::where('stock', '<=', 0)->count();

        $categories = SparePartCategory::orderBy('name')->get();

        $isPrint = $request->boolean('print');
        $spareParts = $isPrint ? (clone $query)->latest()->get() : (clone $query)->latest()->paginate(15);

        return view('admin.reports.inventory', compact(
            'spareParts',
            'totalPartsValue',
            'totalFilteredValue',
            'totalItems',
            'totalStockUnits',
            'lowStockCount',
            'outOfStockCount',
            'categories',
            'categoryId',
            'stockStatus',
            'search',
            'isPrint'
        ));
    }

    /**
     * Laporan Resmi Finansial & Rekapitulasi Kasir
     */
    public function finance(Request $request): View
    {
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);
        $type = $request->input('type', 'all');
        $method = $request->input('method', 'all');
        $search = $request->input('search');

        $query = SalesOrder::with(['customer.user', 'creator', 'payments', 'invoice']);

        // Kasir melihat uang yang diterimanya pada periode ini; selain itu menurut transaksi
        $cashierId = $request->user()->role === 'cashier' ? $request->user()->id : null;
        if ($cashierId) {
            $query->whereHas('payments', fn ($p) => $p->receivedBy($cashierId, $startDate, $endDate));
        } else {
            $query->inPeriod($startDate, $endDate);
        }

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        if ($method && $method !== 'all') {
            $query->whereHas('payments', fn ($p) => $p->where('method', $method));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('customer.user', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Rekapitulasi Finansial
        $paidOrdersQuery = (clone $query)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed]);

        $totalIncome = (int) (clone $paidOrdersQuery)->sum('revenue_idr');
        $totalSubtotal = (int) (clone $paidOrdersQuery)->sum('subtotal_idr');
        $totalDiscount = (int) (clone $paidOrdersQuery)->sum('discount_idr');
        $totalTax = (int) (clone $paidOrdersQuery)->sum('tax_idr');

        $carIncome = (int) (clone $paidOrdersQuery)->where('type', SalesOrderType::CarSales)->sum('revenue_idr');
        $serviceIncome = (int) (clone $paidOrdersQuery)->where('type', SalesOrderType::Service)->sum('revenue_idr');
        $sparepartIncome = (int) (clone $paidOrdersQuery)->whereIn('type', [SalesOrderType::SparePart, SalesOrderType::Pos])->sum('revenue_idr');

        $avgTransaction = (clone $paidOrdersQuery)->count() > 0 ? (int) ($totalIncome / (clone $paidOrdersQuery)->count()) : 0;

        // Jembatan nilai transaksi ke omzet: hak pemilik titip jual dan refund
        $totalGross = (int) (clone $paidOrdersQuery)->sum('total_idr');
        $totalRefunded = (int) (clone $paidOrdersQuery)->sum('refunded_idr');
        $consignorShare = max(0, $totalGross - $totalIncome - $totalRefunded);

        $received = collect();
        if ($cashierId) {
            $received = Payment::receivedBy($cashierId, $startDate, $endDate)
                ->where('paymentable_type', SalesOrder::class)
                ->whereIn('paymentable_id', (clone $query)->pluck('id'))
                ->when($method && $method !== 'all', fn ($q) => $q->where('method', $method))
                ->get()->groupBy('paymentable_id')->map(fn ($rows) => (int) $rows->sum('amount_idr'));
            $totalIncome = (int) $received->sum();
            $avgTransaction = (int) round((float) $received->avg());
        }

        $isPrint = $request->boolean('print');
        $query->orderByRaw('COALESCE(paid_at, created_at) desc');
        $allTransactions = $isPrint ? (clone $query)->get() : (clone $query)->paginate(15);

        return view('admin.reports.finance', compact(
            'allTransactions',
            'cashierId',
            'received',
            'totalGross',
            'totalRefunded',
            'consignorShare',
            'totalIncome',
            'totalSubtotal',
            'totalDiscount',
            'totalTax',
            'carIncome',
            'serviceIncome',
            'sparepartIncome',
            'avgTransaction',
            'startDate',
            'endDate',
            'preset',
            'type',
            'method',
            'search',
            'isPrint'
        ));
    }
}
