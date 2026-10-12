<?php


declare(strict_types=1);


namespace App\Services;


use App\Models\Attendance;
use App\Models\MechanicSchedule;
use App\Models\SalesCommission;
use App\Models\SalesOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class HrService
{
    /**
     * Create mechanic schedule
     */
    public function createSchedule(
        int $userId,
        Carbon $date,
        string $shiftStart,
        string $shiftEnd,
        bool $isAvailable = true
    ): MechanicSchedule {
        return MechanicSchedule::create([
            'user_id' => $userId,
            'date' => $date,
            'shift_start' => $shiftStart,
            'shift_end' => $shiftEnd,
            'is_available' => $isAvailable,
        ]);
    }


    /**
     * Get mechanic schedules for date range
     */
    public function getSchedules(Carbon $startDate, Carbon $endDate): Collection
    {
        return MechanicSchedule::with('user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('shift_start')
            ->get();
    }


    /**
     * Get schedules for specific mechanic
     */
    public function getMechanicSchedules(int $userId, Carbon $startDate, Carbon $endDate): Collection
    {
        return MechanicSchedule::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    }


    /**
     * Record attendance clock in
     */
    public function clockIn(int $userId, Carbon $date, ?string $notes = null): Attendance
    {
        return Attendance::updateOrCreate(
            [
                'user_id' => $userId,
                'date' => $date,
            ],
            [
                'clock_in' => now()->format('H:i:s'),
                'status' => 'present',
                'notes' => $notes,
            ]
        );
    }


    /**
     * Record attendance clock out
     */
    public function clockOut(int $userId, Carbon $date): Attendance
    {
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $date)
            ->first();


        if (!$attendance) {
            throw new \Exception('No clock-in record found for today');
        }


        $attendance->update([
            'clock_out' => now()->format('H:i:s'),
        ]);


        return $attendance;
    }


    /**
     * Get attendance records
     */
    public function getAttendances(
        ?int $userId = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): Collection {
        $query = Attendance::with('user');


        if ($userId) {
            $query->where('user_id', $userId);
        }


        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }


        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }


        return $query->orderBy('date', 'desc')->get();
    }


    /**
     * Get attendance summary for user
     */
    public function getAttendanceSummary(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();


        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $absentDays = $attendances->where('status', 'absent')->count();


        $totalHours = $attendances->sum(function ($attendance) {
            if ($attendance->clock_in && $attendance->clock_out) {
                $clockIn = Carbon::parse($attendance->clock_in);
                $clockOut = Carbon::parse($attendance->clock_out);
                return $clockIn->diffInHours($clockOut);
            }
            return 0;
        });


        return [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'absent_days' => $absentDays,
            'total_hours' => $totalHours,
            'attendance_rate' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
        ];
    }


    /**
     * Calculate sales commission
     */
    public function calculateCommission(
        int $userId,
        Carbon $startDate,
        Carbon $endDate,
        float $commissionRate = 0.02 // 2% default
    ): array {
        $sales = SalesOrder::where('created_by', $userId)
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();


        $totalSales = $sales->sum('total_idr');
        $commission = $totalSales * $commissionRate;


        return [
            'user_id' => $userId,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_sales' => $totalSales,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commission,
            'sales_count' => $sales->count(),
            'sales' => $sales,
        ];
    }


    /**
     * Record sales commission
     */
    public function recordCommission(
        int $userId,
        int $salesOrderId,
        int $amount,
        ?string $notes = null
    ): SalesCommission {
        return SalesCommission::create([
            'user_id' => $userId,
            'sales_order_id' => $salesOrderId,
            'amount_idr' => $amount,
            'status' => 'pending',
            'notes' => $notes,
        ]);
    }


    /**
     * Get commissions for user
     */
    public function getCommissions(
        int $userId,
        ?string $status = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): Collection {
        $query = SalesCommission::with('salesOrder')
            ->where('user_id', $userId);


        if ($status) {
            $query->where('status', $status);
        }


        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }


        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }


        return $query->orderBy('created_at', 'desc')->get();
    }


    /**
     * Mark commission as paid
     */
    public function markCommissionPaid(int $commissionId): SalesCommission
    {
        $commission = SalesCommission::findOrFail($commissionId);
        $commission->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);


        return $commission;
    }


    /**
     * Get available mechanics for date
     */
    public function getAvailableMechanics(Carbon $date): Collection
    {
        return User::where('role', 'mechanic')
            ->whereHas('mechanicSchedules', function ($query) use ($date) {
                $query->where('date', $date)
                      ->where('is_available', true);
            })
            ->get();
    }
}



