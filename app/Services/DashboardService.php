<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AiScanStatus;
use App\Enums\CarUnitStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Enums\ServiceBookingStatus;
use App\Enums\WorkOrderStatus;
use App\Models\AiScan;
use App\Models\ApprovalRequest;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get dashboard statistics
     */
    public function getStats(): array
    {
        return [
            'sales' => $this->getSalesStats(),
            'workshop' => $this->getWorkshopStats(),
            'inventory' => $this->getInventoryStats(),
            'ai_scans' => $this->getAiScanStats(),
            'customers' => $this->getCustomerStats(),
        ];
    }

    /**
     * Get sales statistics
     */
    protected function getSalesStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthSales = SalesOrder::where('type', SalesOrderType::CarSales)
            ->where('status', SalesOrderStatus::Paid)
            ->where('created_at', '>=', $currentMonth)
            ->sum('total_idr');

        $lastMonthSales = SalesOrder::where('type', SalesOrderType::CarSales)
            ->where('status', SalesOrderStatus::Paid)
            ->whereBetween('created_at', [$lastMonth, $currentMonth])
            ->sum('total_idr');

        $carsSold = SalesOrder::where('type', SalesOrderType::CarSales)
            ->where('status', SalesOrderStatus::Paid)
            ->where('created_at', '>=', $currentMonth)
            ->count();

        return [
            'revenue_current_month' => $currentMonthSales,
            'revenue_last_month' => $lastMonthSales,
            'revenue_growth' => $lastMonthSales > 0
                ? round((($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 2)
                : 0,
            'cars_sold' => $carsSold,
            'available_units' => CarUnit::where('status', CarUnitStatus::Available)->count(),
        ];
    }

    /**
     * Get workshop statistics
     */
    protected function getWorkshopStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();

        $workshopRevenue = SalesOrder::where('type', SalesOrderType::Service)
            ->where('status', SalesOrderStatus::Paid)
            ->where('created_at', '>=', $currentMonth)
            ->sum('total_idr');

        $activeWorkOrders = WorkOrder::whereIn('status', [
            WorkOrderStatus::Booking,
            WorkOrderStatus::Queued,
            WorkOrderStatus::InProgress,
        ])->count();

        $bookingsToday = ServiceBooking::whereDate('preferred_date', today())
            ->whereIn('status', [
                ServiceBookingStatus::Pending,
                ServiceBookingStatus::Confirmed,
            ])
            ->count();

        return [
            'revenue_current_month' => $workshopRevenue,
            'active_work_orders' => $activeWorkOrders,
            'bookings_today' => $bookingsToday,
        ];
    }

    /**
     * Get inventory statistics
     */
    protected function getInventoryStats(): array
    {
        $lowStockParts = SparePart::whereColumn('stock', '<=', 'low_stock_threshold')
            ->count();

        $totalPartsValue = SparePart::select(
            DB::raw('SUM(stock * price_idr) as total_value')
        )->value('total_value') ?? 0;

        return [
            'low_stock_parts' => $lowStockParts,
            'total_parts_value' => $totalPartsValue,
            'total_parts_count' => SparePart::count(),
            'total_items' => SparePart::count(),
        ];
    }

    /**
     * Get AI scan statistics
     */
    protected function getAiScanStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();

        $totalScans = AiScan::where('created_at', '>=', $currentMonth)->count();
        $completedScans = AiScan::where('status', AiScanStatus::Completed)
            ->where('created_at', '>=', $currentMonth)
            ->count();

        return [
            'total_scans' => $totalScans,
            'completed_scans' => $completedScans,
            'success_rate' => $totalScans > 0
                ? round(($completedScans / $totalScans) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get customer statistics
     */
    protected function getCustomerStats(): array
    {
        $totalCustomers = Customer::count();

        // Repeat customer rate (customers with more than 1 transaction)
        $repeatCustomers = Customer::whereHas('salesOrders', function ($query) {
            $query->where('status', SalesOrderStatus::Paid)
                ->groupBy('customer_id')
                ->havingRaw('COUNT(*) > 1');
        })->count();

        return [
            'total_customers' => $totalCustomers,
            'repeat_customers' => $repeatCustomers,
            'repeat_rate' => $totalCustomers > 0
                ? round(($repeatCustomers / $totalCustomers) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get pending approvals
     */
    public function getPendingApprovals(int $limit = 5): Collection
    {
        return ApprovalRequest::where('status', 'pending')
            ->with('requester')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 10): Collection
    {
        // This would integrate with audit log system
        return collect([]);
    }

    /**
     * Get owner dashboard data with dynamic period filters and reporting analytics
     */
    public function getOwnerDashboardData(Request $request): array
    {
        $preset = $request->input('preset', 'this_month');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        [$startDate, $endDate, $prevStartDate, $prevEndDate, $preset, $periodLabel] = $this->resolveOwnerDateRange(
            $preset,
            $startDateInput,
            $endDateInput
        );

        // Base query for current period
        $salesQuery = SalesOrder::whereBetween('created_at', [$startDate, $endDate]);

        // Revenue metrics (Paid & Completed)
        $paidOrders = (clone $salesQuery)->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])->get();
        $totalRevenue = (int) $paidOrders->sum('total_idr');
        $totalOrdersCount = $paidOrders->count();

        // Breakdown per Division
        $carOrders = $paidOrders->where('type', SalesOrderType::CarSales);
        $carRevenue = (int) $carOrders->sum('total_idr');
        $carsSoldCount = $carOrders->count();
        $avgCarPrice = $carsSoldCount > 0 ? (int) round($carRevenue / $carsSoldCount) : 0;

        $workshopOrders = $paidOrders->where('type', SalesOrderType::Service);
        $workshopRevenue = (int) $workshopOrders->sum('total_idr');
        $workshopOrdersCount = $workshopOrders->count();

        $partOrders = $paidOrders->where('type', SalesOrderType::SparePart);
        $partRevenue = (int) $partOrders->sum('total_idr');
        $partOrdersCount = $partOrders->count();

        // Pending Receivables
        $pendingQuery = (clone $salesQuery)->where('status', SalesOrderStatus::Pending);
        $pendingRevenue = (int) $pendingQuery->sum('total_idr');
        $pendingCount = $pendingQuery->count();

        // Growth vs Previous Period
        $prevRevenue = (int) SalesOrder::whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])
            ->sum('total_idr');

        $revenueGrowth = $prevRevenue > 0
            ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : ($totalRevenue > 0 ? 100.0 : 0.0);

        // Expense / Purchase Orders in period
        $purchasesQuery = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate]);
        $totalPurchases = (int) (clone $purchasesQuery)->sum('total_idr');
        $netInflow = $totalRevenue - $totalPurchases;

        // Workshop Operations
        $completedWorkOrders = WorkOrder::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', [WorkOrderStatus::Completed, WorkOrderStatus::Delivered])
            ->count();
        $activeWorkOrders = WorkOrder::whereIn('status', [
            WorkOrderStatus::Booking,
            WorkOrderStatus::Queued,
            WorkOrderStatus::InProgress,
            WorkOrderStatus::QualityControl,
        ])->count();
        $newBookings = ServiceBooking::whereBetween('created_at', [$startDate, $endDate])->count();

        // Assets & Inventory Snapshot (Current State)
        $readyCars = CarUnit::where('status', CarUnitStatus::Available)->get(['price_sell_idr']);
        $readyCarsCount = $readyCars->count();
        $readyCarsValuation = (int) $readyCars->sum('price_sell_idr');

        $totalPartsStock = (int) SparePart::sum('stock');
        $totalPartsValuation = (int) (SparePart::sum(DB::raw('stock * price_idr')) ?? 0);
        $lowStockCount = SparePart::whereColumn('stock', '<=', 'low_stock_threshold')->count();

        // Customers / CRM
        $totalCustomers = Customer::count();
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        $repeatCustomers = Customer::whereHas('salesOrders', function ($query) {
            $query->whereIn('status', [SalesOrderStatus::Paid, SalesOrderStatus::Completed])
                ->groupBy('customer_id')
                ->havingRaw('COUNT(*) > 1');
        })->count();

        // Trend Data for Chart
        $trendData = $this->generateTrendData($paidOrders, $startDate, $endDate);

        // Recent High-Value Transactions in period
        $recentTransactions = SalesOrder::with(['customer.user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(8)
            ->get();

        return compact(
            'preset',
            'startDate',
            'endDate',
            'periodLabel',
            'totalRevenue',
            'prevRevenue',
            'revenueGrowth',
            'totalOrdersCount',
            'carRevenue',
            'carsSoldCount',
            'avgCarPrice',
            'workshopRevenue',
            'workshopOrdersCount',
            'completedWorkOrders',
            'activeWorkOrders',
            'newBookings',
            'partRevenue',
            'partOrdersCount',
            'totalPurchases',
            'netInflow',
            'pendingRevenue',
            'pendingCount',
            'readyCarsCount',
            'readyCarsValuation',
            'totalPartsStock',
            'totalPartsValuation',
            'lowStockCount',
            'totalCustomers',
            'newCustomers',
            'repeatCustomers',
            'trendData',
            'recentTransactions'
        );
    }

    /**
     * Resolves date range and previous comparison range for owner reporting
     */
    public function resolveOwnerDateRange(string $preset, ?string $startDateInput, ?string $endDateInput): array
    {
        $now = Carbon::now();

        if ($startDateInput || $endDateInput) {
            $startDate = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : $now->copy()->startOfMonth()->startOfDay();
            $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : $now->copy()->endOfDay();
            if ($startDate->gt($endDate)) {
                [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
            }
            $preset = 'custom';
            $diffInDays = max(1, $startDate->diffInDays($endDate) + 1);
            $prevStartDate = $startDate->copy()->subDays($diffInDays)->startOfDay();
            $prevEndDate = $startDate->copy()->subDay()->endOfDay();
            $periodLabel = $startDate->translatedFormat('d M Y').' s/d '.$endDate->translatedFormat('d M Y');
        } else {
            switch ($preset) {
                case 'today':
                    $startDate = Carbon::today()->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    $prevStartDate = Carbon::yesterday()->startOfDay();
                    $prevEndDate = Carbon::yesterday()->endOfDay();
                    $periodLabel = 'Hari Ini ('.$startDate->translatedFormat('d F Y').')';
                    break;

                case 'yesterday':
                    $startDate = Carbon::yesterday()->startOfDay();
                    $endDate = Carbon::yesterday()->endOfDay();
                    $prevStartDate = Carbon::yesterday()->subDay()->startOfDay();
                    $prevEndDate = Carbon::yesterday()->subDay()->endOfDay();
                    $periodLabel = 'Kemarin ('.$startDate->translatedFormat('d F Y').')';
                    break;

                case 'last_7_days':
                    $startDate = Carbon::now()->subDays(6)->startOfDay();
                    $endDate = Carbon::now()->endOfDay();
                    $prevStartDate = $startDate->copy()->subDays(7)->startOfDay();
                    $prevEndDate = $startDate->copy()->subDay()->endOfDay();
                    $periodLabel = '7 Hari Terakhir ('.$startDate->translatedFormat('d M').' — '.$endDate->translatedFormat('d M Y').')';
                    break;

                case 'last_30_days':
                    $startDate = Carbon::now()->subDays(29)->startOfDay();
                    $endDate = Carbon::now()->endOfDay();
                    $prevStartDate = $startDate->copy()->subDays(30)->startOfDay();
                    $prevEndDate = $startDate->copy()->subDay()->endOfDay();
                    $periodLabel = '30 Hari Terakhir ('.$startDate->translatedFormat('d M').' — '.$endDate->translatedFormat('d M Y').')';
                    break;

                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth()->startOfDay();
                    $endDate = Carbon::now()->subMonth()->endOfMonth()->endOfDay();
                    $prevStartDate = Carbon::now()->subMonths(2)->startOfMonth()->startOfDay();
                    $prevEndDate = Carbon::now()->subMonths(2)->endOfMonth()->endOfDay();
                    $periodLabel = 'Bulan Lalu ('.$startDate->translatedFormat('F Y').')';
                    break;

                case 'this_year':
                    $startDate = Carbon::now()->startOfYear()->startOfDay();
                    $endDate = Carbon::now()->endOfYear()->endOfDay();
                    $prevStartDate = Carbon::now()->subYear()->startOfYear()->startOfDay();
                    $prevEndDate = Carbon::now()->subYear()->endOfYear()->endOfDay();
                    $periodLabel = 'Tahun Ini ('.$startDate->format('Y').')';
                    break;

                case 'last_year':
                    $startDate = Carbon::now()->subYear()->startOfYear()->startOfDay();
                    $endDate = Carbon::now()->subYear()->endOfYear()->endOfDay();
                    $prevStartDate = Carbon::now()->subYears(2)->startOfYear()->startOfDay();
                    $prevEndDate = Carbon::now()->subYears(2)->endOfYear()->endOfDay();
                    $periodLabel = 'Tahun Lalu ('.$startDate->format('Y').')';
                    break;

                case 'this_month':
                default:
                    $preset = 'this_month';
                    $startDate = Carbon::now()->startOfMonth()->startOfDay();
                    $endDate = Carbon::now()->endOfMonth()->endOfDay();
                    $prevStartDate = Carbon::now()->subMonth()->startOfMonth()->startOfDay();
                    $prevEndDate = Carbon::now()->subMonth()->endOfMonth()->endOfDay();
                    $periodLabel = 'Bulan Ini ('.$startDate->translatedFormat('F Y').')';
                    break;
            }
        }

        return [$startDate, $endDate, $prevStartDate, $prevEndDate, $preset, $periodLabel];
    }

    /**
     * Generate daily or monthly trend points for chart
     */
    protected function generateTrendData(Collection $paidOrders, Carbon $startDate, Carbon $endDate): array
    {
        $diffDays = $startDate->diffInDays($endDate);
        $isMonthly = $diffDays > 35;
        $points = [];

        if (! $isMonthly) {
            $grouped = $paidOrders->groupBy(fn ($order) => $order->created_at->format('Y-m-d'));
            $cursor = $startDate->copy();
            while ($cursor->lte($endDate)) {
                $key = $cursor->format('Y-m-d');
                $ordersInDay = $grouped->get($key, collect([]));
                $points[] = [
                    'label' => $cursor->translatedFormat('d M'),
                    'full_date' => $cursor->translatedFormat('d F Y'),
                    'car_rev' => (int) $ordersInDay->where('type', SalesOrderType::CarSales)->sum('total_idr'),
                    'workshop_rev' => (int) $ordersInDay->where('type', SalesOrderType::Service)->sum('total_idr'),
                    'part_rev' => (int) $ordersInDay->where('type', SalesOrderType::SparePart)->sum('total_idr'),
                    'total_rev' => (int) $ordersInDay->sum('total_idr'),
                ];
                $cursor->addDay();
            }
        } else {
            $grouped = $paidOrders->groupBy(fn ($order) => $order->created_at->format('Y-m'));
            $cursor = $startDate->copy()->startOfMonth();
            while ($cursor->lte($endDate)) {
                $key = $cursor->format('Y-m');
                $ordersInMonth = $grouped->get($key, collect([]));
                $points[] = [
                    'label' => $cursor->translatedFormat('M Y'),
                    'full_date' => $cursor->translatedFormat('F Y'),
                    'car_rev' => (int) $ordersInMonth->where('type', SalesOrderType::CarSales)->sum('total_idr'),
                    'workshop_rev' => (int) $ordersInMonth->where('type', SalesOrderType::Service)->sum('total_idr'),
                    'part_rev' => (int) $ordersInMonth->where('type', SalesOrderType::SparePart)->sum('total_idr'),
                    'total_rev' => (int) $ordersInMonth->sum('total_idr'),
                ];
                $cursor->addMonth();
            }
        }

        return $points;
    }
}
