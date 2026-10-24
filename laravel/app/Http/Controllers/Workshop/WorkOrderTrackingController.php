<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkOrderTrackingController extends Controller
{
    /**
     * Tampilkan status dan detail pengerjaan work order.
     */
    public function show(Request $request, WorkOrder $workOrder): View
    {
        $user = $request->user();

        // Pastikan work order terkait dengan kendaraan milik customer jika diakses oleh role customer
        if ($user->role === 'customer') {
            $customer = $user->customer;
            $vehicleCustomerId = $workOrder->vehicle?->customer_id;
            $bookingCustomerId = $workOrder->serviceBooking?->customer_id;

            if (! $customer || ($vehicleCustomerId !== $customer->id && $bookingCustomerId !== $customer->id)) {
                abort(403, 'Akses tidak diizinkan.');
            }
        }

        $workOrder->load(['vehicle.customer', 'serviceBooking', 'items', 'mechanic']);

        // Riwayat waktu transisi status dari AuditLog
        $statusLogs = \App\Models\AuditLog::where('model_id', $workOrder->id)
            ->whereIn('action', ['work_order.status_updated', 'status_changed'])
            ->orderBy('created_at', 'asc')
            ->get();

        $statusTimestamps = [];
        foreach ($statusLogs as $log) {
            if (preg_match('/to ([a-z_]+)$/i', trim($log->description ?? ''), $matches)) {
                $statusTimestamps[strtolower($matches[1])] = $log->created_at;
            }
        }

        return view('workshop.tracking', compact('workOrder', 'statusTimestamps'));
    }

    /**
     * Tampilkan timeline progress servis work order.
     */
    public function timeline(Request $request, WorkOrder $workOrder): View
    {
        return $this->show($request, $workOrder);
    }
}
