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
use App\Models\WorkOrder;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(Request $request): View
    {
        $stats = $this->dashboardService->getStats();

        $totalRevenue = (int) SalesOrder::whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('total_idr');
        $carSalesCount = SalesOrder::where('type', SalesOrderType::CarSales)
            ->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])
            ->count();
        $completedWorkOrders = WorkOrder::where('status', WorkOrderStatus::Completed)->count();
        $totalCustomers = Customer::count();

        $recentSales = SalesOrder::with(['customer.user'])
            ->latest()
            ->take(6)
            ->get();

        $recentWorkOrders = WorkOrder::with(['vehicle.customer.user', 'mechanic'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.reports.index', compact(
            'stats',
            'totalRevenue',
            'carSalesCount',
            'completedWorkOrders',
            'totalCustomers',
            'recentSales',
            'recentWorkOrders'
        ));
    }

    public function sales(Request $request): View
    {
        $salesOrders = SalesOrder::where('type', SalesOrderType::CarSales)
            ->with(['customer.user', 'items'])
            ->latest()
            ->paginate(15);

        $totalCarRevenue = (int) SalesOrder::where('type', SalesOrderType::CarSales)
            ->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])
            ->sum('total_idr');
        $unitsSold = SalesOrder::where('type', SalesOrderType::CarSales)
            ->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])
            ->count();
        $availableUnits = CarUnit::where('status', CarUnitStatus::Available)->count();

        return view('admin.reports.sales', compact('salesOrders', 'totalCarRevenue', 'unitsSold', 'availableUnits'));
    }

    public function workshop(Request $request): View
    {
        $workOrders = WorkOrder::with(['vehicle.customer.user', 'mechanic'])
            ->latest()
            ->paginate(15);

        $totalWorkshopRevenue = (int) SalesOrder::where('type', SalesOrderType::Service)
            ->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])
            ->sum('total_idr');
        $completedRepairs = WorkOrder::where('status', WorkOrderStatus::Completed)->count();
        $activeRepairs = WorkOrder::whereIn('status', [WorkOrderStatus::Queued, WorkOrderStatus::InProgress])->count();

        return view('admin.reports.workshop', compact('workOrders', 'totalWorkshopRevenue', 'completedRepairs', 'activeRepairs'));
    }

    public function inventory(Request $request): View
    {
        $spareParts = SparePart::query()
            ->latest()
            ->paginate(15);

        $totalPartsValue = (int) (SparePart::selectRaw('SUM(stock * price_idr) as total_val')->value('total_val') ?? 0);
        $totalItems = SparePart::count();
        $lowStockCount = SparePart::whereColumn('stock', '<=', 'low_stock_threshold')->count();

        return view('admin.reports.inventory', compact('spareParts', 'totalPartsValue', 'totalItems', 'lowStockCount'));
    }

    public function finance(Request $request): View
    {
        $allTransactions = SalesOrder::with(['customer.user'])
            ->latest()
            ->paginate(15);

        $totalIncome = (int) SalesOrder::whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('total_idr');
        $carIncome = (int) SalesOrder::where('type', SalesOrderType::CarSales)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('total_idr');
        $serviceIncome = (int) SalesOrder::where('type', SalesOrderType::Service)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('total_idr');
        $sparepartIncome = (int) SalesOrder::whereIn('type', [SalesOrderType::SparePart, SalesOrderType::Pos])->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->sum('total_idr');

        return view('admin.reports.finance', compact('allTransactions', 'totalIncome', 'carIncome', 'serviceIncome', 'sparepartIncome'));
    }
}
