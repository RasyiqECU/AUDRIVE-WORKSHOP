<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CustomerVehicle;
use App\Models\User;

class CustomerVehiclePolicy
{
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'mechanic', 'sales'])) {
            return true;
        }

        return $user->role === 'customer' && $user->customer !== null;
    }

    public function view(User $user, CustomerVehicle $vehicle): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'mechanic', 'sales'])) {
            return true;
        }

        return $user->customer && $vehicle->customer_id === $user->customer->id;
    }

    public function create(User $user): bool
    {
        if ($user->role === 'customer' && $user->customer) {
            return true;
        }

        return in_array($user->role, ['super-admin', 'manager', 'mechanic', 'sales']);
    }

    public function update(User $user, CustomerVehicle $vehicle): bool
    {
        if (in_array($user->role, ['super-admin', 'manager'])) {
            return true;
        }

        return $user->customer && $vehicle->customer_id === $user->customer->id;
    }

    public function delete(User $user, CustomerVehicle $vehicle): bool
    {
        if (in_array($user->role, ['super-admin', 'manager'])) {
            return true;
        }

        return $user->customer && $vehicle->customer_id === $user->customer->id;
    }
}
