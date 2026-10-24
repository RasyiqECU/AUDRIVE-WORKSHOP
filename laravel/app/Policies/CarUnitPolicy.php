<?php


declare(strict_types=1);


namespace App\Policies;


use App\Models\CarUnit;
use App\Models\User;


class CarUnitPolicy
{
    public function viewAny(User $user): bool
    {
        // Semua user bisa melihat listing mobil
        return true;
    }


    public function view(User $user, CarUnit $carUnit): bool
    {
        // Semua user bisa melihat detail mobil
        return true;
    }


    public function create(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'sales']);
    }


    public function update(User $user, CarUnit $carUnit): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'sales']);
    }


    public function delete(User $user, CarUnit $carUnit): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function restore(User $user, CarUnit $carUnit): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function forceDelete(User $user, CarUnit $carUnit): bool
    {
        return $user->role === 'super-admin';
    }


    public function updateStatus(User $user, CarUnit $carUnit): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'sales']);
    }


    public function inspect(User $user, CarUnit $carUnit): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'sales', 'mechanic']);
    }
}



