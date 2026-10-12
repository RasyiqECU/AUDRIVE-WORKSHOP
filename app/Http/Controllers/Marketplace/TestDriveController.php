<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CarUnit;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestDriveController extends Controller
{
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

        $customer = $request->user()?->customer;
        if (!$customer) {
            return back()->with('error', 'Silakan lengkapi profil customer Anda terlebih dahulu.');
        }

        $datetime = Carbon::parse($request->scheduled_at . ' ' . $request->time_slot);

        Appointment::create([
            'type'         => 'test_drive',
            'car_unit_id'  => $car->id,
            'customer_id'  => $customer->id,
            'scheduled_at' => $datetime,
            'status'       => AppointmentStatus::Pending,
            'notes'        => $request->notes,
        ]);

        return redirect()
            ->route('marketplace.show', $car->slug)
            ->with('success', 'Jadwal Test Drive berhasil diajukan. Tim kami akan menghubungi Anda.');
    }
}
