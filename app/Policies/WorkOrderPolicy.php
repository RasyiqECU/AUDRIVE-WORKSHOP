<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\WorkOrderStatus;
use App\Models\User;
use App\Models\WorkOrder;

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
        if (in_array($user->role, ['super-admin', 'manager', 'cashier'])) {
            return true;
        }

        if ($user->role === 'mechanic') {
            return $workOrder->assigned_mechanic_id === $user->id;
        }

        // Customer bisa melihat work order miliknya
        if ($user->role === 'customer' && $user->customer) {
            return $workOrder->vehicle && $workOrder->vehicle->customer_id === $user->customer->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'cashier']);
    }

    public function manage(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        if ($user->role === 'super-admin') {
            return true;
        }

        // Mekanik hanya boleh mengubah work order yang di-assign ke dirinya dan status belum selesai/dibayar/diserahkan
        if ($user->role === 'mechanic') {
            $statusVal = $workOrder->status instanceof WorkOrderStatus ? $workOrder->status->value : (string) $workOrder->status;

            return $workOrder->assigned_mechanic_id === $user->id
                && in_array($statusVal, ['queued', 'booking', 'in_progress', 'quality_control'], true);
        }

        return false;
    }

    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return $user->role === 'super-admin';
    }

    public function assignMechanic(User $user, WorkOrder $workOrder): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }

    public function updateStatus(User $user, WorkOrder $workOrder): bool
    {
        if ($user->role === 'super-admin') {
            return true;
        }

        // Mekanik hanya boleh mengubah status work order yang di-assign ke dirinya
        if ($user->role === 'mechanic') {
            $statusVal = $workOrder->status instanceof WorkOrderStatus ? $workOrder->status->value : (string) $workOrder->status;

            return $workOrder->assigned_mechanic_id === $user->id
                && in_array($statusVal, ['queued', 'booking', 'in_progress', 'quality_control'], true);
        }

        return false;
    }

    public function deliver(User $user, WorkOrder $workOrder): bool
    {
        return in_array($user->role, ['super-admin', 'cashier']);
    }
}
