<?php


declare(strict_types=1);


namespace App\Policies;


use App\Models\SparePart;
use App\Models\User;


class SparePartPolicy
{
    public function viewAny(User $user): bool
    {
        // Semua user bisa melihat spare part
        return true;
    }


    public function view(User $user, SparePart $sparePart): bool
    {
        return true;
    }


    public function create(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'inventory']);
    }


    public function update(User $user, SparePart $sparePart): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'inventory']);
    }


    public function delete(User $user, SparePart $sparePart): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function restore(User $user, SparePart $sparePart): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function forceDelete(User $user, SparePart $sparePart): bool
    {
        return $user->role === 'super-admin';
    }


    public function manageStock(User $user, SparePart $sparePart): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'inventory']);
    }
}



