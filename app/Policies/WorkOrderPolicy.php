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
        if (in_array($user->role, ['super-admin', 'cashier', 'sales', 'mechanic'])) {
            return true;
        }

        // Customer bisa melihat work order miliknya
        return $user->role === 'customer' && $user->customer !== null;
    }

    public function view(User $user, WorkOrder $workOrder): bool
    {
        if (in_array($user->role, ['super-admin', 'cashier', 'sales'])) {
            return true;
        }

        if ($user->role === 'mechanic') {
            return $this->handledBy($user, $workOrder);
        }

        // Customer bisa melihat work order miliknya
        if ($user->role === 'customer' && $user->customer) {
            return $workOrder->vehicle && $workOrder->vehicle->customer_id === $user->customer->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'mechanic']);
    }

    /** QC akhir oleh Kepala Mekanik yang memegang work order. */
    public function qualityControl(User $user, WorkOrder $workOrder): bool
    {
        return $user->role === 'super-admin'
            || ($user->role === 'mechanic' && $workOrder->assigned_mechanic_id === $user->id);
    }

    public function manage(User $user): bool
    {
        return $user->role === 'super-admin';
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        if ($user->role === 'super-admin') {
            return true;
        }

        // Kepala Mekanik hanya boleh mengubah work order yang di-assign ke dirinya dan status belum selesai/dibayar/diserahkan
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
        return $user->role === 'super-admin'
            || ($user->role === 'mechanic' && $this->handledBy($user, $workOrder));
    }

    public function updateStatus(User $user, WorkOrder $workOrder): bool
    {
        if ($user->role === 'super-admin') {
            return true;
        }

        // Kepala Mekanik hanya boleh mengubah status work order yang di-assign ke dirinya
        if ($user->role === 'mechanic') {
            $statusVal = $workOrder->status instanceof WorkOrderStatus ? $workOrder->status->value : (string) $workOrder->status;

            return $workOrder->assigned_mechanic_id === $user->id
                && in_array($statusVal, ['queued', 'booking', 'in_progress', 'quality_control'], true);
        }

        return false;
    }

    public function deliver(User $user, WorkOrder $workOrder): bool
    {
        return in_array($user->role, ['super-admin', 'cashier', 'sales']);
    }

    /** Work order milik sendiri, atau yang belum punya penanggung jawab. */
    private function handledBy(User $user, WorkOrder $workOrder): bool
    {
        return $workOrder->assigned_mechanic_id === null || $workOrder->assigned_mechanic_id === $user->id;
    }
}
