<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $customer = $user->customer ?? $user->getOrCreateCustomerProfile();
        $tickets = $customer->supportTickets()->latest()->paginate(10);

        return view('customer.tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        return view('customer.tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:150',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'description' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $customer = $user->customer ?? $user->getOrCreateCustomerProfile();

        $customer->supportTickets()->create([
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return redirect()->route('customer.tickets.index')->with('success', 'Tiket bantuan berhasil dibuat.');
    }

    public function show(Request $request, SupportTicket $ticket): View
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer || $ticket->customer_id !== $customer->id) {
            abort(403);
        }

        return view('customer.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer || $ticket->customer_id !== $customer->id) {
            abort(403);
        }

        $request->validate(['message' => 'required|string|max:2000']);

        $ticket->messages()->create([
            'user_id' => $user->id,
            'sender_type' => 'customer',
            'message' => $request->message,
        ]);

        // Jika status resolved/closed, buka kembali jadi in_progress
        if (in_array($ticket->status->value, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Balasan berhasil dikirim.');
    }
}
