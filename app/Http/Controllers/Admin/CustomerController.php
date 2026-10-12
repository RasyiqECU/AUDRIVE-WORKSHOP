<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->with(['user', 'vehicles'])
            ->withCount('vehicles')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load(['user', 'vehicles', 'serviceBookings', 'loyaltyTransactions']);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        $customer->load('user');
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'loyalty_tier' => 'nullable|string|in:bronze,silver,gold,platinum',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function vehicles(Customer $customer): View
    {
        $vehicles = $customer->vehicles()->latest()->get();
        return view('admin.customers.vehicles', compact('customer', 'vehicles'));
    }

    public function history(Customer $customer): View
    {
        $bookings = $customer->serviceBookings()->with('vehicle')->latest()->get();
        return view('admin.customers.history', compact('customer', 'bookings'));
    }
}
