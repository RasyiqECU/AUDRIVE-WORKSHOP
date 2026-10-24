<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupportTicket::with(['customer.user', 'assignee'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('customer.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        $stats = [
            'total'       => SupportTicket::count(),
            'open'        => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved'    => SupportTicket::where('status', 'resolved')->count(),
        ];

        return view('admin.tickets.index', compact('tickets', 'stats'));
    }

    public function show(SupportTicket $ticket): View
    {
        $ticket->load(['customer.user', 'assignee', 'messages.user']);
        $staffMembers = User::whereIn('role', ['super-admin', 'manager'])->orderBy('name')->get();

        return view('admin.tickets.show', compact('ticket', 'staffMembers'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:3000',
            'status'  => 'nullable|string|in:open,in_progress,resolved,closed',
        ]);

        $user = $request->user();

        $ticket->messages()->create([
            'user_id'     => $user->id,
            'sender_type' => 'staff',
            'message'     => $request->message,
        ]);

        $updates = [];
        if ($request->filled('status')) {
            $updates['status'] = $request->status;
            if ($request->status === 'resolved' && !$ticket->resolved_at) {
                $updates['resolved_at'] = now();
            }
        } elseif ($ticket->status->value === 'open') {
            $updates['status'] = 'in_progress';
        }

        if (!$ticket->assigned_to) {
            $updates['assigned_to'] = $user->id;
        }

        if (!empty($updates)) {
            $ticket->update($updates);
        }

        // Kirim notifikasi ke customer
        if ($ticket->customer && $ticket->customer->user_id) {
            Notification::create([
                'user_id' => $ticket->customer->user_id,
                'title'   => 'Balasan Tiket Support #' . $ticket->code,
                'message' => "Tim AUDrive telah membalas tiket Anda: '{$ticket->subject}'",
                'type'    => 'ticket_reply',
                'data'    => ['ticket_id' => $ticket->id, 'code' => $ticket->code],
            ]);
        }

        return back()->with('success', 'Balasan berhasil dikirim ke customer.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'status'      => 'required|string|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $updates = [
            'status' => $request->status,
        ];

        if ($request->has('assigned_to')) {
            $updates['assigned_to'] = $request->assigned_to ?: null;
        }

        if ($request->status === 'resolved') {
            $updates['resolved_at'] = now();
        } elseif ($request->status === 'open') {
            $updates['resolved_at'] = null;
        }

        $ticket->update($updates);

        return back()->with('success', 'Status tiket berhasil diperbarui.');
    }
}
