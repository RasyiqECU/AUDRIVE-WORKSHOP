<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Promotion;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $customer = $user->customer ?? Customer::firstOrCreate(['user_id' => $user->id]);
        $claimedVouchers = $customer->voucherRedemptions()->with('voucher')->latest()->get();
        $availableVouchers = Voucher::active()->latest()->get();

        return view('customer.vouchers.index', compact('claimedVouchers', 'availableVouchers'));
    }

    public function claim(Request $request, Voucher $voucher): RedirectResponse
    {
        $user = $request->user();
        $customer = $user->customer ?? Customer::firstOrCreate(['user_id' => $user->id]);

        if (!$voucher->isAvailable()) {
            return back()->with('error', 'Voucher tidak tersedia atau sudah habis.');
        }

        $customer->voucherRedemptions()->create([
            'voucher_id' => $voucher->id,
            'status' => 'active',
            'redeemed_at' => now(),
        ]);

        $voucher->increment('used_count');

        return back()->with('success', 'Voucher berhasil diklaim!');
    }

    public function promotions(): View
    {
        $promotions = Promotion::where('is_active', true)->latest()->get();
        return view('customer.vouchers.promotions', compact('promotions'));
    }
}
