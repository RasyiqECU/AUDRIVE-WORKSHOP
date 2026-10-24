<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\Notification\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Display list of appointments (Test Drive & Inspeksi).
     */
    public function index(Request $request): View
    {
        $query = Appointment::with(['customer.user', 'carUnit.photos'])->latest('scheduled_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('customer.user', fn($u) => $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
                    ->orWhereHas('carUnit', fn($c) => $c->where('brand', 'like', "%{$s}%")->orWhere('model', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"));
            });
        }

        $appointments = $query->paginate(15)->withQueryString();

        $pendingCount = Appointment::where('status', AppointmentStatus::Pending)->count();
        $confirmedCount = Appointment::where('status', AppointmentStatus::Confirmed)->count();
        $totalCount = Appointment::count();

        return view('admin.appointments.index', compact('appointments', 'pendingCount', 'confirmedCount', 'totalCount'));
    }

    /**
     * Display detail of an appointment.
     */
    public function show(Appointment $appointment): View
    {
        $appointment->load(['customer.user', 'carUnit.photos']);

        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Update appointment status (confirm, complete, cancel).
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
            'notes'  => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = AppointmentStatus::from($request->status);
        $appointment->update([
            'status' => $newStatus,
            'notes'  => $request->notes ?? $appointment->notes,
        ]);

        $carName = $appointment->carUnit ? ($appointment->carUnit->brand . ' ' . $appointment->carUnit->model) : 'Unit Mobil';
        $scheduledTime = $appointment->scheduled_at->format('d M Y, H:i') . ' WIB';

        // Kirim Notifikasi ke Customer
        if ($appointment->customer?->user) {
            $notifType = match($newStatus) {
                AppointmentStatus::Confirmed => 'test_drive_confirmed',
                AppointmentStatus::Completed => 'test_drive_completed',
                default                      => 'test_drive_status',
            };

            $notifTitle = match($newStatus) {
                AppointmentStatus::Confirmed => "Jadwal Test Drive Dikonfirmasi: {$carName}",
                AppointmentStatus::Completed => "Uji Kendaraan Selesai: {$carName}",
                AppointmentStatus::Cancelled => "Jadwal Test Drive Dibatalkan: {$carName}",
                default                      => "Update Status Test Drive: {$carName}",
            };

            $notifMessage = match($newStatus) {
                AppointmentStatus::Confirmed => "Jadwal Test Drive untuk {$carName} telah DIKONFIRMASI untuk tanggal {$scheduledTime}. Tim Sales kami siap menyambut Anda di showroom AUDrive.",
                AppointmentStatus::Completed => "Sesi uji kendaraan (test drive) unit {$carName} telah selesai dilaksanakan. Terima kasih atas antusiasme Anda.",
                AppointmentStatus::Cancelled => "Jadwal Test Drive unit {$carName} pada {$scheduledTime} telah dibatalkan.",
                default                      => "Status jadwal permohonan test drive Anda telah diperbarui.",
            };

            $this->notificationService->send($appointment->customer->user, $notifType, [
                'title'        => $notifTitle,
                'message'      => $notifMessage,
                'car_name'     => $carName,
                'scheduled_at' => $scheduledTime,
                'action_url'   => $appointment->carUnit ? route('marketplace.show', $appointment->carUnit->slug) : null,
            ]);
        }

        return back()->with('success', "Status jadwal uji kendaraan berhasil diperbarui menjadi {$newStatus->label()}.");
    }
}
