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
        $customer = $request->user()->customer;

        // Pastikan work order terkait dengan kendaraan milik customer
        if ($workOrder->vehicle?->customer_id !== $customer->id && $workOrder->serviceBooking?->customer_id !== $customer->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $workOrder->load(['vehicle', 'serviceBooking', 'items', 'mechanic']);

        return view('workshop.tracking', compact('workOrder'));
    }

    /**
     * Tampilkan timeline progress servis work order.
     */
    public function timeline(Request $request, WorkOrder $workOrder): View
    {
        return $this->show($request, $workOrder);
    }
}
