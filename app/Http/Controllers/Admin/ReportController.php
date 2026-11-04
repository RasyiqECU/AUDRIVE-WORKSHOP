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
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
    public function index(Request $request): View
    {
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);

        $salesQuery = SalesOrder::query();
        $woQuery = WorkOrder::query();

        if ($startDate && $endDate) {
            $salesQuery->whereBetween('created_at', [$startDate, $endDate]);
            $woQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Total Omzet Terverifikasi (Paid / Completed)
        $totalRevenue = (int) (clone $salesQuery)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('total_idr');
        $totalDiscounts = (int) (clone $salesQuery)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('discount_idr');
        $totalTaxes = (int) (clone $salesQuery)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('tax_idr');
        $totalOrdersCount = (clone $salesQuery)->count();

        // Breakdown per Divisi
        $carRevenue = (int) (clone $salesQuery)->where('type', SalesOrderType::CarSales)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('total_idr');
        $carSalesCount = (clone $salesQuery)->where('type', SalesOrderType::CarSales)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->count();

        $workshopRevenue = (int) (clone $salesQuery)->where('type', SalesOrderType::Service)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('total_idr');
        $completedWorkOrders = (clone $woQuery)->where('status', WorkOrderStatus::Completed)->count();
        $activeWorkOrders = (clone $woQuery)->whereIn('status', [WorkOrderStatus::Queued, WorkOrderStatus::InProgress])->count();

        $sparePartRevenue = (int) (clone $salesQuery)->whereIn('type', [SalesOrderType::SparePart, SalesOrderType::Pos])->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('total_idr');

        $totalCustomers = Customer::count();

        // Recent Verified Sales Transactions
        $recentSales = (clone $salesQuery)->with(['customer.user', 'creator'])
            ->latest()
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
            'preset'
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
            ->with(['customer.user', 'items.carUnit', 'creator', 'payments']);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
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
        $totalCarRevenue = (int) (clone $query)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('total_idr');
        $totalDiscountGiven = (int) (clone $query)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('discount_idr');
        $unitsSold = (clone $query)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->count();
        $averageSellingPrice = $unitsSold > 0 ? (int) ($totalCarRevenue / $unitsSold) : 0;
        $availableUnits = CarUnit::where('status', CarUnitStatus::Available)->count();

        // Paginate for web, or fetch all if in print mode
        $isPrint = $request->boolean('print');
        $salesOrders = $isPrint ? (clone $query)->latest()->get() : (clone $query)->latest()->paginate(15);

        return view('admin.reports.sales', compact(
            'salesOrders',
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

        // Workshop Analytics for filtered dataset
        $completedRepairs = (clone $query)->where('status', WorkOrderStatus::Completed)->count();
        $activeRepairs = (clone $query)->whereIn('status', [WorkOrderStatus::Queued, WorkOrderStatus::InProgress])->count();
        $totalWorkshopRevenue = (int) (clone $query)->where('status', WorkOrderStatus::Completed)->sum('total_idr');
        $totalJasaRevenue = (int) (clone $query)->where('status', WorkOrderStatus::Completed)->sum('total_jasa_idr');
        $totalPartRevenue = (int) (clone $query)->where('status', WorkOrderStatus::Completed)->sum('total_part_idr');
        $avgRepairCost = $completedRepairs > 0 ? (int) ($totalWorkshopRevenue / $completedRepairs) : 0;

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

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
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

        $totalIncome = (int) (clone $paidOrdersQuery)->sum('total_idr');
        $totalSubtotal = (int) (clone $paidOrdersQuery)->sum('subtotal_idr');
        $totalDiscount = (int) (clone $paidOrdersQuery)->sum('discount_idr');
        $totalTax = (int) (clone $paidOrdersQuery)->sum('tax_idr');

        $carIncome = (int) (clone $paidOrdersQuery)->where('type', SalesOrderType::CarSales)->sum('total_idr');
        $serviceIncome = (int) (clone $paidOrdersQuery)->where('type', SalesOrderType::Service)->sum('total_idr');
        $sparepartIncome = (int) (clone $paidOrdersQuery)->whereIn('type', [SalesOrderType::SparePart, SalesOrderType::Pos])->sum('total_idr');

        $avgTransaction = (clone $paidOrdersQuery)->count() > 0 ? (int) ($totalIncome / (clone $paidOrdersQuery)->count()) : 0;

        $isPrint = $request->boolean('print');
        $allTransactions = $isPrint ? (clone $query)->latest()->get() : (clone $query)->latest()->paginate(15);

        return view('admin.reports.finance', compact(
            'allTransactions',
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
