<?php


declare(strict_types=1);


namespace App\Policies;


use App\Models\AiScan;
use App\Models\User;


class AiScanPolicy
{
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, ['super-admin', 'manager'])) {
            return true;
        }


        // Mechanic bisa melihat semua scan
        if ($user->role === 'mechanic') {
            return true;
        }


        // Customer bisa melihat scan miliknya
        return $user->role === 'customer' && $user->customer !== null;
    }


    public function view(User $user, AiScan $scan): bool
    {
        if (in_array($user->role, ['super-admin', 'manager', 'mechanic'])) {
            return true;
        }


        // Customer hanya bisa melihat scan miliknya
        return $user->customer && $scan->customer_id === $user->customer->id;
    }


    public function create(User $user): bool
    {
        // Customer dan mechanic bisa membuat scan
        if ($user->role === 'customer' && $user->customer) {
            return true;
        }


        return in_array($user->role, ['super-admin', 'manager', 'mechanic']);
    }


    public function update(User $user, AiScan $scan): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'mechanic']);
    }


    public function delete(User $user, AiScan $scan): bool
    {
        return in_array($user->role, ['super-admin', 'manager']);
    }


    public function review(User $user, AiScan $scan): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'mechanic']);
    }
}



