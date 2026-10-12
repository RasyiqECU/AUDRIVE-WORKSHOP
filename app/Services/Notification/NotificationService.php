<?php


declare(strict_types=1);


namespace App\Services\Notification;


use App\Models\User;
use App\Services\Notification\Channels\DatabaseNotificationChannel;
use App\Services\Notification\Channels\EmailNotificationChannel;
use App\Services\Notification\Channels\WhatsAppNotificationChannel;
use App\Services\Notification\Contracts\NotificationChannelContract;
use App\Services\Notification\Contracts\NotificationServiceContract;
use Illuminate\Support\Facades\Log;


class NotificationService implements NotificationServiceContract
{
    protected array $channels = [];


    public function __construct()
    {
        $this->channels = [
            'email' => EmailNotificationChannel::class,
            'database' => DatabaseNotificationChannel::class,
            'whatsapp' => WhatsAppNotificationChannel::class,
        ];
    }


    /**
     * Send notification to user via configured channels
     */
    public function send(User $user, string $type, array $data = []): bool
    {
        $channels = $this->getChannelsForType($type);
        $results = [];


        foreach ($channels as $channelName) {
            $results[$channelName] = $this->sendVia($channelName, $user, $type, $data);
        }


        // Return true if at least one channel succeeded
        return in_array(true, $results, true);
    }


    /**
     * Send notification via specific channel
     */
    public function sendVia(string $channel, User $user, string $type, array $data = []): bool
    {
        try {
            if (!isset($this->channels[$channel])) {
                Log::warning("Notification channel not found: {$channel}");
                return false;
            }


            $channelClass = $this->channels[$channel];
            $channelInstance = app($channelClass);


            return $channelInstance->send($user, $type, $data);


        } catch (\Exception $e) {
            Log::error("Notification send failed", [
                'channel' => $channel,
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }


    /**
     * Send notification to multiple users
     */
    public function sendToMany(array $users, string $type, array $data = []): array
    {
        $results = [];


        foreach ($users as $user) {
            $results[$user->id] = $this->send($user, $type, $data);
        }


        return $results;
    }


    /**
     * Get channels for notification type
     */
    protected function getChannelsForType(string $type): array
    {
        // Default channels for each type
        $channelMap = [
            'booking_created' => ['database', 'email', 'whatsapp'],
            'booking_confirmed' => ['database', 'email', 'whatsapp'],
            'work_order_status_changed' => ['database', 'email'],
            'payment_success' => ['database', 'email', 'whatsapp'],
            'payment_failed' => ['database', 'email'],
            'offer_accepted' => ['database', 'email', 'whatsapp'],
            'offer_rejected' => ['database', 'email'],
            'ai_scan_completed' => ['database', 'email'],
            'low_stock_alert' => ['database', 'email'],
            'service_reminder' => ['database', 'email', 'whatsapp'],
            'stnk_reminder' => ['database', 'email', 'whatsapp'],
            'warranty_reminder' => ['database', 'email', 'whatsapp'],
        ];


        return $channelMap[$type] ?? ['database'];
    }


    /**
     * Get available channel
     */
    public function getChannel(string $name): ?NotificationChannelContract
    {
        if (!isset($this->channels[$name])) {
            return null;
        }


        return app($this->channels[$name]);
    }
}



