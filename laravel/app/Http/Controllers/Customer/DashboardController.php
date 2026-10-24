<?php


declare(strict_types=1);


namespace App\Http\Controllers\Customer;


use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;


class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'customer') {
            if ($user->role === 'mechanic') {
                return redirect()->route('workshop.index');
            }
            return redirect()->route('admin.dashboard');
        }

        $customer = $user->getOrCreateCustomerProfile();

        $customer->loadCount('vehicles');


        $activeBookings = $customer->serviceBookings()
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->with(['vehicle', 'workOrder'])
            ->latest()
            ->limit(3)
            ->get();


        $recentScans = $customer->aiScans()
            ->latest()
            ->limit(3)
            ->get();


        $notifications = $user
            ->notifications()
            ->latest()
            ->limit(5)
            ->get();


        $activeVouchers = $customer->voucherRedemptions()
            ->with('voucher')
            ->whereHas('voucher', fn($q) => $q->active())
            ->latest()
            ->limit(3)
            ->get();


        return view('customer.dashboard', compact(
            'customer',
            'activeBookings',
            'recentScans',
            'notifications',
            'activeVouchers'
        ));
    }

    public function notifications(Request $request): View
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->paginate(15);
        return view('customer.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}



