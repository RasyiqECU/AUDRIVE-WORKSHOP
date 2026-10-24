<?php


declare(strict_types=1);


namespace App\Policies;


use App\Models\SalesOrder;
use App\Models\User;


class SalesOrderPolicy
{
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'cashier', 'sales'])) {
            return true;
        }


        return $user->role === 'customer' && $user->customer !== null;
    }


    public function view(User $user, SalesOrder $order): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'cashier', 'sales'])) {
            return true;
        }


        return $user->customer && $order->customer_id === $user->customer->id;
    }


    public function create(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'cashier', 'sales']);
    }


    public function update(User $user, SalesOrder $order): bool
    {
        if (in_array($user->role, ['super-admin', 'manager'])) {
            return true;
        }


        // Cashier bisa update status pembayaran
        if ($user->role === 'cashier') {
            return in_array($order->status->value, ['pending', 'processing']);
        }


        // Sales bisa update order yang masih pending
        if ($user->role === 'sales') {
            return $order->status->value === 'pending';
        }


        return false;
    }


    public function delete(User $user, SalesOrder $order): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function processPayment(User $user, SalesOrder $order): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'cashier']);
    }


    public function cancel(User $user, SalesOrder $order): bool
    {
        if (in_array($user->role, ['super-admin', 'manager'])) {
            return true;
        }


        // Customer bisa cancel order yang masih pending
        return $user->customer
            && $order->customer_id === $user->customer->id
            && $order->status->value === 'pending';
    }
}



