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
    public function index(Request $request): View
    {
        $user = $request->user();
        $customer = $user->customer ?? Customer::firstOrCreate(['user_id' => $user->id]);
        $vehicles = $customer->vehicles()->with('serviceBookings')->get();

        return view('customer.reminders.index', compact('customer', 'vehicles'));
    }

    public function dismiss(Request $request, string $reminder): RedirectResponse
    {
        return back()->with('success', 'Pengingat berhasil diabaikan.');
    }
}
