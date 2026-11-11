<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderController extends Controller
{
    /** Tipe notifikasi reminder yang bersifat personal untuk customer. */
    protected const REMINDER_TYPES = ['service_reminder', 'stnk_reminder', 'warranty_reminder'];

    public function index(Request $request): View
    {
        $user = $request->user();
        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $customer = $user->customer ?? $user->getOrCreateCustomerProfile();
        $vehicles = $customer->vehicles()->with('serviceBookings')->get();

        // Reminder aktif yang dikirim sistem (belum dibaca) berdasarkan tipe
        $activeReminders = $user->unreadNotifications()
            ->where(function ($query) {
                foreach (self::REMINDER_TYPES as $index => $type) {
                    $index === 0 ? $query->where('data->type', $type) : $query->orWhere('data->type', $type);
                }
            })
            ->latest()
            ->get();

        return view('customer.reminders.index', compact('customer', 'vehicles', 'activeReminders'));
    }

    /**
     * Tandai reminder untuk kendaraan tertentu sebagai telah dibaca.
     */
    public function dismiss(Request $request, string $vehicle): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $customer = $user->customer;

        // Kendaraan harus milik customer yang login
        $isOwnedVehicle = $customer && $customer->vehicles()->whereKey($vehicle)->exists();

        if ($isOwnedVehicle) {
            $user->unreadNotifications()
                ->where(function ($query) {
                    foreach (self::REMINDER_TYPES as $index => $type) {
                        $index === 0 ? $query->where('data->type', $type) : $query->orWhere('data->type', $type);
                    }
                })
                ->where('data->vehicle_id', (int) $vehicle)
                ->get()
                ->each->markAsRead();
        }

        return back()->with('success', 'Pengingat berhasil diabaikan.');
    }
}
