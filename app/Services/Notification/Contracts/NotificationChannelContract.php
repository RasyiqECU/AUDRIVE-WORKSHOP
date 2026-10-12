<?php


declare(strict_types=1);


namespace App\Services\Notification\Contracts;


use App\Models\User;


interface NotificationChannelContract
{
    /**
     * Send notification via this channel
     */
    public function send(User $user, string $type, array $data = []): bool;


    /**
     * Get channel name
     */
    public function getName(): string;
}



