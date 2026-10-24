<?php


declare(strict_types=1);


namespace App\Policies;


use App\Models\SupportTicket;
use App\Models\User;


class SupportTicketPolicy
{
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['super-admin', 'manager'])) {
            return true;
        }


        return $user->role === 'customer' && $user->customer !== null;
    }


    public function view(User $user, SupportTicket $ticket): bool
    {
        if (in_array($user->role, ['super-admin', 'manager'])) {
            return true;
        }


        // Customer hanya bisa melihat tiket miliknya
        return $user->customer && $ticket->customer_id === $user->customer->id;
    }


    public function create(User $user): bool
    {
        return $user->role === 'customer' && $user->customer !== null;
    }


    public function update(User $user, SupportTicket $ticket): bool
    {
        if (in_array($user->role, ['super-admin', 'manager'])) {
            return true;
        }


        // Customer bisa update tiket yang masih open
        return $user->customer
            && $ticket->customer_id === $user->customer->id
            && in_array($ticket->status->value, ['open', 'in_progress']);
    }


    public function delete(User $user, SupportTicket $ticket): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function assign(User $user, SupportTicket $ticket): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function resolve(User $user, SupportTicket $ticket): bool
    {
        if (in_array($user->role, ['super-admin', 'manager'])) {
            return true;
        }


        // Assigned user bisa resolve
        return $ticket->assigned_to === $user->id;
    }
}



