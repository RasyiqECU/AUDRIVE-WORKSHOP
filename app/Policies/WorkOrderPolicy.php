<?php


declare(strict_types=1);


namespace App\Policies;


use App\Models\WorkOrder;
use App\Models\User;


class WorkOrderPolicy
{
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'cashier', 'mechanic'])) {
            return true;
        }

        // Customer bisa melihat work order miliknya
        return $user->role === 'customer' && $user->customer !== null;
    }

    public function view(User $user, WorkOrder $workOrder): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'cashier', 'mechanic'])) {
            return true;
        }

        // Customer bisa melihat work order miliknya
        if ($user->role === 'customer' && $user->customer) {
            return $workOrder->vehicle && $workOrder->vehicle->customer_id === $user->customer->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'cashier', 'mechanic']);
    }

    public function manage(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'cashier', 'mechanic']);
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'cashier', 'mechanic'])) {
            return true;
        }

        return false;
    }

    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }

    public function assignMechanic(User $user, WorkOrder $workOrder): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }

    public function updateStatus(User $user, WorkOrder $workOrder): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'cashier', 'mechanic'])) {
            return true;
        }

        return false;
    }
}



