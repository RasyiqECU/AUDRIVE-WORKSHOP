<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()->with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', $search)
                  ->orWhere('model_type', 'like', $search)
                  ->orWhere('ip_address', 'like', $search);
            });
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        $actions = AuditLog::select('action')->distinct()->pluck('action');
        $users = User::select('id', 'name', 'role')->orderBy('name')->get();

        $totalLogs = AuditLog::count();
        $todayLogs = AuditLog::whereDate('created_at', today())->count();

        return view('admin.audit-logs.index', compact('logs', 'actions', 'users', 'totalLogs', 'todayLogs'));
    }
}
