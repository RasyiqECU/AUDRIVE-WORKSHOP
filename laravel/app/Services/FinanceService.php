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
use App\Models\SalesOrder;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class FinanceService
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
}



