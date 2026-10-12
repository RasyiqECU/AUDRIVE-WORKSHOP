<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $customer = $user->customer ?? Customer::firstOrCreate(['user_id' => $user->id]);
        $transactions = $customer->loyaltyTransactions()->latest()->limit(10)->get();
        $availableVouchers = Voucher::active()->where('points_cost', '>', 0)->latest()->get();

        return view('customer.loyalty.index', compact('customer', 'transactions', 'availableVouchers'));
    }

    public function history(Request $request): View
    {
        $user = $request->user();
        $customer = $user->customer ?? Customer::firstOrCreate(['user_id' => $user->id]);
        $transactions = $customer->loyaltyTransactions()->latest()->paginate(15);

        return view('customer.loyalty.history', compact('customer', 'transactions'));
    }

    public function redeem(Request $request): RedirectResponse
    {
        $request->validate([
            'voucher_id' => 'required|exists:vouchers,id',
        ]);

        $user = $request->user();
        $customer = $user->customer ?? Customer::firstOrCreate(['user_id' => $user->id]);
        $voucher = Voucher::findOrFail($request->voucher_id);

        if ($customer->loyalty_points < ($voucher->points_cost ?? 0)) {
            return back()->with('error', 'Poin loyalitas Anda tidak mencukupi.');
        }

        $customer->decrement('loyalty_points', $voucher->points_cost ?? 0);
        $customer->loyaltyTransactions()->create([
            'type' => 'redemption',
            'points' => -($voucher->points_cost ?? 0),
            'description' => 'Penukaran voucher: ' . $voucher->name,
        ]);

        $customer->voucherRedemptions()->create([
            'voucher_id' => $voucher->id,
            'status' => 'active',
            'redeemed_at' => now(),
        ]);

        return back()->with('success', 'Voucher berhasil ditukar dengan poin.');
    }
}
