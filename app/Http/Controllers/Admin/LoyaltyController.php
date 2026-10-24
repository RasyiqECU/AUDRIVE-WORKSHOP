<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use App\Models\Voucher;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function index(Request $request): View
    {
        $customerQuery = Customer::with('user')->orderByDesc('loyalty_points');

        if ($request->filled('search')) {
            $search = $request->search;
            $customerQuery->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $customers = $customerQuery->paginate(15)->withQueryString();

        $stats = [
            'total_points'     => (int) Customer::sum('loyalty_points'),
            'total_earned'     => (int) LoyaltyTransaction::where('points', '>', 0)->sum('points'),
            'total_redeemed'   => (int) abs((int) LoyaltyTransaction::where('points', '<', 0)->sum('points')),
            'reward_vouchers'  => Voucher::where('points_cost', '>', 0)->where('is_active', true)->count(),
        ];

        $rewardVouchers = Voucher::where('points_cost', '>', 0)->orderBy('points_cost')->take(6)->get();

        return view('admin.loyalty.index', compact('customers', 'stats', 'rewardVouchers'));
    }

    public function history(Request $request): View
    {
        $query = LoyaltyTransaction::with('customer.user')->latest();

        if ($request->filled('type')) {
            if ($request->type === 'earned') {
                $query->where('points', '>', 0);
            } elseif ($request->type === 'redeemed') {
                $query->where('points', '<', 0);
            } else {
                $query->where('type', $request->type);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('customer.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('admin.loyalty.history', compact('transactions'));
    }

    public function adjust(Request $request, Customer $customer): RedirectResponse
    {
        $request->validate([
            'action'      => 'required|in:add,deduct',
            'points'      => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        $action = $request->action;
        $points = (int) $request->points;
        $description = trim($request->description);

        if ($action === 'deduct') {
            if ($customer->loyalty_points < $points) {
                return back()->with('error', "Gagal memotong poin: Saldo poin customer hanya {$customer->loyalty_points} poin.");
            }
            $customer->decrement('loyalty_points', $points);
            $pointsChange = -$points;
            $type = 'adjustment_deduct';
            $msgTitle = "Poin Loyalitas Dipotong";
            $msgText = "Saldo loyalitas Anda dipotong {$points} poin. Keterangan: {$description}";
        } else {
            $customer->increment('loyalty_points', $points);
            $pointsChange = $points;
            $type = 'bonus';
            $msgTitle = "Bonus Poin Loyalitas AUDrive!";
            $msgText = "Selamat! Anda mendapatkan bonus {$points} poin loyalitas. Keterangan: {$description}";
        }

        LoyaltyTransaction::create([
            'customer_id'  => $customer->id,
            'type'         => $type,
            'points'       => $pointsChange,
            'description'  => $description . " (Oleh Admin: " . auth()->user()->name . ")",
        ]);

        if ($customer->user_id) {
            Notification::create([
                'user_id' => $customer->user_id,
                'title'   => $msgTitle,
                'message' => $msgText,
                'type'    => 'loyalty',
                'data'    => ['points' => $pointsChange, 'current_balance' => $customer->loyalty_points],
            ]);
        }

        return back()->with('success', "Poin untuk {$customer->user->name} berhasil diperbarui.");
    }
}
