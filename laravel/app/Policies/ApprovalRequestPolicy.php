<?php


declare(strict_types=1);


namespace App\Policies;


use App\Models\ApprovalRequest;
use App\Models\User;


class ApprovalRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function view(User $user, ApprovalRequest $request): bool
    {
        // Requester bisa melihat request miliknya
        if ($request->requested_by === $user->id) {
            return true;
        }


        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function create(User $user): bool
    {
        // Semua staff bisa membuat approval request
        return in_array($user->role, ['super-admin', 'manager', 'sales', 'cashier', 'inventory', 'mechanic']);
    }


    public function update(User $user, ApprovalRequest $approvalRequest): bool
    {
        // Hanya manager dan super-admin yang bisa approve/reject
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function delete(User $user, ApprovalRequest $approvalRequest): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function approve(User $user, ApprovalRequest $approvalRequest): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function reject(User $user, ApprovalRequest $approvalRequest): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }
}



