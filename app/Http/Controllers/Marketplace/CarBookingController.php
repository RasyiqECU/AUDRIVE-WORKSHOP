<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\CarUnit;
use App\Services\CarBookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CarBookingController extends Controller
{
    public function store(Request $request, CarUnit $car, CarBookingService $bookings): RedirectResponse
    {
        $customer = $request->user()->customer;
        if (! $customer) {
            return back()->with('error', 'Booking hanya untuk akun customer.');
        }

        $data = $request->validate([
            'payment_channel' => 'required|in:cashier,online_transfer',
            'fulfillment_method' => 'required|in:self_pickup,delivery',
            'shipping_address' => 'required_if:fulfillment_method,delivery|nullable|string|max:500',
        ]);

        try {
            $order = $bookings->book($customer, $car, $data['payment_channel'], $data['fulfillment_method'], $data['shipping_address'] ?? null);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('parts.checkout.payment', $order)->with('success', 'Booking dibuat. Bayar booking fee untuk mengunci unit.');
    }
}
