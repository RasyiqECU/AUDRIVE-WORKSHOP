<?php


declare(strict_types=1);


namespace App\Policies;


use App\Models\Offer;
use App\Models\User;


class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'sales']);
    }


    public function view(User $user, Offer $offer): bool
    {
        // Sales bisa melihat semua offer
        if (in_array($user->role, ['super-admin', 'manager', 'sales'])) {
            return true;
        }


        // Customer hanya bisa melihat offer miliknya
        return $user->customer && $offer->customer_id === $user->customer->id;
    }


    public function create(User $user): bool
    {
        // Customer bisa membuat offer
        return $user->role === 'customer' && $user->customer !== null;
    }


    public function update(User $user, Offer $offer): bool
    {
        // Sales bisa update offer (accept/reject)
        if (in_array($user->role, ['super-admin', 'manager', 'sales'])) {
            return true;
        }


        // Customer hanya bisa update offer miliknya yang masih pending
        return $user->customer
            && $offer->customer_id === $user->customer->id
            && $offer->status->value === 'pending';
    }


    public function delete(User $user, Offer $offer): bool
    {
        // Sales bisa delete offer
        if (in_array($user->role, ['super-admin', 'manager', 'sales'])) {
            return true;
        }


        // Customer hanya bisa delete offer miliknya yang masih pending
        return $user->customer
            && $offer->customer_id === $user->customer->id
            && $offer->status->value === 'pending';
    }


    public function review(User $user, Offer $offer): bool
    {
        return in_array($user->role, ['super-admin', 'manager', 'sales']);
    }
}



