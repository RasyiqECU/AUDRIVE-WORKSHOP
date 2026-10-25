<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Enums\AppointmentStatus;
use App\Enums\CarUnitStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CarUnit;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestDriveController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function create(CarUnit $car): View
    {
        return view('marketplace.test-drive', compact('car'));
    }

    public function store(Request $request, CarUnit $car): RedirectResponse
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:today',
            'time_slot'    => 'required|string',
            'notes'        => 'nullable|string|max:500',
        ]);

        if (!$car->status || $car->status !== CarUnitStatus::Available) {
            return back()->withErrors(['car_unit' => 'Unit tidak tersedia untuk test drive.']);
        }

        $customer = $request->user()?->customer;
        if (!$customer) {
            return back()->with('error', 'Silakan lengkapi profil customer Anda terlebih dahulu.');
        }

        $datetime = Carbon::parse($request->scheduled_at)->setTimeFromTimeString($request->time_slot);

        $appointment = Appointment::create([
            'type'         => 'test_drive',
            'car_unit_id'  => $car->id,
            'customer_id'  => $customer->id,
            'scheduled_at' => $datetime,
            'status'       => AppointmentStatus::Pending,
            'notes'        => $request->notes,
        ]);

        $carName = "{$car->brand} {$car->model}";
        $scheduledStr = $datetime->format('d M Y, H:i') . ' WIB';

        // 1. Notifikasi ke Tim Sales & Super Admin
        $this->notificationService->notifyRoles(['sales', 'super-admin'], 'test_drive_requested', [
            'title'         => "Permohonan Test Drive Baru: {$carName}",
            'customer_name' => $request->user()->name,
            'car_name'      => $carName,
            'scheduled_at'  => $scheduledStr,
            'action_url'    => route('admin.appointments.show', $appointment->id),
        ]);

        // 2. Notifikasi ke Customer pemohon
        $this->notificationService->send($request->user(), 'test_drive_requested', [
            'title'        => "Permohonan Test Drive Terkirim: {$carName}",
            'message'      => "Permohonan jadwal uji kendaraan untuk unit {$carName} pada {$scheduledStr} berhasil diajukan. Tim Sales kami akan segera mengonfirmasi ketersediaan unit.",
            'car_name'     => $carName,
            'scheduled_at' => $scheduledStr,
            'action_url'   => route('marketplace.show', $car->slug),
        ]);

        return redirect()
            ->route('marketplace.show', $car->slug)
            ->with('success', 'Jadwal Test Drive berhasil diajukan. Tim Sales AUDrive akan segera mengonfirmasi jadwal Anda.');
    }
}
