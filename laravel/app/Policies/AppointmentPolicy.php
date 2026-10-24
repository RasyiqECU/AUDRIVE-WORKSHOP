<?php


declare(strict_types=1);


namespace App\Policies;


use App\Models\Appointment;
use App\Models\User;


class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'sales']);
    }


    public function view(User $user, Appointment $appointment): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'sales'])) {
            return true;
        }


        return $user->customer && $appointment->customer_id === $user->customer->id;
    }


    public function create(User $user): bool
    {
        return $user->role === 'customer' && $user->customer !== null;
    }


    public function update(User $user, Appointment $appointment): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'sales'])) {
            return true;
        }


        return $user->customer
            && $appointment->customer_id === $user->customer->id
            && in_array($appointment->status->value, ['pending', 'confirmed']);
    }


    public function delete(User $user, Appointment $appointment): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'sales'])) {
            return true;
        }


        return $user->customer
            && $appointment->customer_id === $user->customer->id
            && $appointment->status->value === 'pending';
    }


    public function confirm(User $user, Appointment $appointment): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'sales']);
    }
}



