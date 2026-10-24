<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $customer = $user->customer ?? $user->getOrCreateCustomerProfile();
        $transactions = $customer->loyaltyTransactions()->latest()->limit(10)->get();
        $availableVouchers = Voucher::active()->where('points_cost', '>', 0)->latest()->get();

        return view('customer.loyalty.index', compact('customer', 'transactions', 'availableVouchers'));
    }

    public function history(Request $request): View
    {
        $user = $request->user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $customer = $user->customer ?? $user->getOrCreateCustomerProfile();
        $transactions = $customer->loyaltyTransactions()->latest()->paginate(15);

        return view('customer.loyalty.history', compact('customer', 'transactions'));
    }

    public function redeem(Request $request): RedirectResponse
    {
        $request->validate([
            'voucher_id' => 'required|exists:vouchers,id',
        ]);

        $user = $request->user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $customer = $user->customer ?? $user->getOrCreateCustomerProfile();
        $voucher = Voucher::findOrFail($request->voucher_id);

        $pointsCost = (int) ($voucher->points_cost ?? 0);

        if ($pointsCost <= 0) {
            return back()->with('error', 'Voucher ini tidak dapat ditukar dengan poin.');
        }

        if ($customer->loyalty_points < $pointsCost) {
            return back()->with('error', 'Poin loyalitas Anda tidak mencukupi.');
        }

        DB::transaction(function () use ($customer, $voucher, $pointsCost) {
            $customer->decrement('loyalty_points', $pointsCost);

            $customer->loyaltyTransactions()->create([
                'type' => 'redemption',
                'points' => -$pointsCost,
                'description' => 'Penukaran voucher: ' . ($voucher->code),
            ]);

            $customer->voucherRedemptions()->create([
                'voucher_id' => $voucher->id,
                'redeemed_at' => now(),
            ]);

            $voucher->increment('used_count');
        });

        return back()->with('success', 'Voucher ' . $voucher->code . ' berhasil ditukar dengan poin.');
    }
}
