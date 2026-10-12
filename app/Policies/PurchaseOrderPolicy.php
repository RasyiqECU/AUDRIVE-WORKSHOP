<?php


declare(strict_types=1);


namespace App\Policies;


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
        return in_array($user->role, ['super-admin', 'manager', 'inventory']);
    }


    public function update(User $user, PurchaseOrder $po): bool
    {
        if (in_array($user->role, ['super-admin', 'manager'])) {
            return true;
        }


        // Inventory bisa update PO yang masih draft
        return $user->role === 'inventory' && $po->status->value === 'draft';
    }


    public function delete(User $user, PurchaseOrder $po): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function send(User $user, PurchaseOrder $po): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'inventory']);
    }


    public function receive(User $user, PurchaseOrder $po): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'inventory']);
    }
}



