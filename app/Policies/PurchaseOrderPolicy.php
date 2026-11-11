<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'inventory']);
    }

    public function view(User $user, PurchaseOrder $po): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'inventory']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'inventory']);
    }

    public function update(User $user, PurchaseOrder $po): bool
    {
        // Inventory bisa update PO yang masih draft
        $statusVal = $po->status instanceof PurchaseOrderStatus ? $po->status->value : (string) $po->status;

        return in_array($user->role, ['super-admin', 'inventory']) && $statusVal === 'draft';
    }

    public function delete(User $user, PurchaseOrder $po): bool
    {
        return in_array($user->role, ['super-admin', 'inventory']);
    }

    public function send(User $user, PurchaseOrder $po): bool
    {
        return in_array($user->role, ['super-admin', 'inventory']);
    }

    public function receive(User $user, PurchaseOrder $po): bool
    {
        return in_array($user->role, ['super-admin', 'inventory']);
    }

    public function approve(User $user, PurchaseOrder $po): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }
}
