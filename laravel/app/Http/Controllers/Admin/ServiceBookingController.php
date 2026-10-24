<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Services\Notification\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceBookingController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

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
        $serviceBooking->load(['customer.user', 'vehicle']);

        try {
            $vehicleName = trim(($serviceBooking->vehicle?->brand ?? '') . ' ' . ($serviceBooking->vehicle?->model ?? 'Kendaraan'));
            $bookingDate = $serviceBooking->booking_date ? (is_string($serviceBooking->booking_date) ? $serviceBooking->booking_date : $serviceBooking->booking_date->format('d M Y H:i')) : '-';

            // Kirim notifikasi ke Customer jika ada user-nya
            if ($serviceBooking->customer?->user) {
                $type = $request->status === 'confirmed' ? 'service_booking_confirmed' : 'work_order_status_changed';
                $this->notificationService->notifyCustomer(
                    $serviceBooking->customer->user,
                    $type,
                    [
                        'title'      => $request->status === 'confirmed' ? 'Booking Servis Dikonfirmasi' : 'Update Status Booking Servis',
                        'message'    => "Status booking servis kendaraan {$vehicleName} telah diperbarui menjadi: " . strtoupper($request->status),
                        'date'       => $bookingDate,
                        'vehicle'    => $vehicleName,
                        'status'     => strtoupper($request->status),
                        'action_url' => route('workshop.my-bookings'),
                    ]
                );
            }

            // Notifikasi ke staf bengkel mekanik & super-admin
            $this->notificationService->notifyRoles(
                ['mechanic', 'super-admin'],
                'service_booking_confirmed',
                [
                    'title'      => 'Status Booking Servis: ' . strtoupper($request->status),
                    'message'    => "Booking kendaraan {$vehicleName} (" . ($serviceBooking->customer?->user?->name ?? 'Customer') . ") status kini: " . strtoupper($request->status),
                    'date'       => $bookingDate,
                    'vehicle'    => $vehicleName,
                    'status'     => strtoupper($request->status),
                    'action_url' => route('admin.service-bookings.show', $serviceBooking->id),
                ]
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi update booking servis: ' . $e->getMessage());
        }

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }
}
