<?php


declare(strict_types=1);


namespace App\Policies;


use App\Models\ServiceBooking;
use App\Models\User;


class ServiceBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'mechanic', 'cashier']);
    }


    public function view(User $user, ServiceBooking $booking): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'mechanic', 'cashier'])) {
            return true;
        }


        return $user->customer && $booking->customer_id === $user->customer->id;
    }


    public function create(User $user): bool
    {
        return $user->role === 'customer' && $user->customer !== null;
    }


    public function update(User $user, ServiceBooking $booking): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'cashier', 'mechanic'])) {
            return true;
        }

        // Customer bisa update booking yang masih pending
        $statusVal = $booking->status instanceof \App\Enums\ServiceBookingStatus ? $booking->status->value : $booking->status;
        return $user->customer
            && $booking->customer_id === $user->customer->id
            && $statusVal === 'pending';
    }

    public function delete(User $user, ServiceBooking $booking): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'cashier'])) {
            return true;
        }

        $statusVal = $booking->status instanceof \App\Enums\ServiceBookingStatus ? $booking->status->value : $booking->status;
        return $user->customer
            && $booking->customer_id === $user->customer->id
            && $statusVal === 'pending';
    }

    public function confirm(User $user, ServiceBooking $booking): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'cashier', 'mechanic']);
    }

    public function assignMechanic(User $user, ServiceBooking $booking): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }
}



