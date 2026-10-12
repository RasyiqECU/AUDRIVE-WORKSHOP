<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceBookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = ServiceBooking::query()
            ->with(['customer.user', 'vehicle'])
            ->latest()
            ->paginate(15);

        return view('admin.service-bookings.index', compact('bookings'));
    }

    public function show(ServiceBooking $serviceBooking): View
    {
        $serviceBooking->load(['customer.user', 'vehicle', 'workOrder']);
        return view('admin.service-bookings.show', compact('serviceBooking'));
    }

    public function updateStatus(Request $request, ServiceBooking $serviceBooking): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $serviceBooking->update(['status' => $request->status]);

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }
}
