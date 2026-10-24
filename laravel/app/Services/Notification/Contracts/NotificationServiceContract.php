<?php


declare(strict_types=1);


namespace App\Services\Notification\Contracts;


use App\Models\User;


interface NotificationServiceContract
{
    /**
     * Send notification to user
     */
    public function send(User $user, string $type, array $data = []): bool;


    /**
     * Send notification via specific channel
     */
    public function sendVia(string $channel, User $user, string $type, array $data = []): bool;


    /**
     * Send notification to multiple users
     */
    public function sendToMany(array $users, string $type, array $data = []): array;

    /**
     * Send notification to all active users with specific roles
     */
    public function notifyRoles(array $roles, string $type, array $data = []): array;

    /**
     * Send notification to a customer
     */
    public function notifyCustomer(\App\Models\Customer|User $recipient, string $type, array $data = []): bool;
}



