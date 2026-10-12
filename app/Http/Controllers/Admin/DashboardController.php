<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display admin dashboard
     */
    public function index(): View
    {
        $stats = $this->dashboardService->getStats();
        $pendingApprovals = $this->dashboardService->getPendingApprovals();
        $recentActivities = $this->dashboardService->getRecentActivities();

        return view('admin.dashboard.index', compact(
            'stats',
            'pendingApprovals',
            'recentActivities'
        ));
    }

    /**
     * Display admin incoming notifications
     */
    public function notifications(Request $request): View
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->paginate(15);
        $unreadCount = $user->unreadNotifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark single notification as read
     */
    public function markRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notifikasi ditandai telah dibaca.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }

    /**
     * Get dashboard stats via API
     */
    public function stats()
    {
        return response()->json($this->dashboardService->getStats());
    }
}
