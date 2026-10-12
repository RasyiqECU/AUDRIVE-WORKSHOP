<?php


declare(strict_types=1);


namespace App\Services\Notification\Channels;


use App\Models\User;
use App\Services\Notification\Contracts\NotificationChannelContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class EmailNotificationChannel implements NotificationChannelContract
{
    /**
     * Send notification via email
     */
    public function send(User $user, string $type, array $data = []): bool
    {
        try {
            $emailClass = $this->getEmailClass($type);


            if (!$emailClass) {
                Log::warning("Email notification class not found for type: {$type}");
                return false;
            }


            Mail::to($user->email)->send(new $emailClass($user, $data));


            Log::info("Email notification sent", [
                'user_id' => $user->id,
                'type' => $type,
                'email' => $user->email,
            ]);


            return true;


        } catch (\Exception $e) {
            Log::error("Email notification failed", [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }


    /**
     * Get channel name
     */
    public function getName(): string
    {
        return 'email';
    }


    /**
     * Get email class for notification type
     */
    protected function getEmailClass(string $type): ?string
    {
        $emailMap = [
            'booking_created' => \App\Mail\BookingCreatedMail::class,
            'booking_confirmed' => \App\Mail\BookingConfirmedMail::class,
            'work_order_status_changed' => \App\Mail\WorkOrderStatusChangedMail::class,
            'payment_success' => \App\Mail\PaymentSuccessMail::class,
            'payment_failed' => \App\Mail\PaymentFailedMail::class,
            'offer_accepted' => \App\Mail\OfferAcceptedMail::class,
            'offer_rejected' => \App\Mail\OfferRejectedMail::class,
            'ai_scan_completed' => \App\Mail\AiScanCompletedMail::class,
            'low_stock_alert' => \App\Mail\LowStockAlertMail::class,
            'service_reminder' => \App\Mail\ServiceReminderMail::class,
            'stnk_reminder' => \App\Mail\StnkReminderMail::class,
            'warranty_reminder' => \App\Mail\WarrantyReminderMail::class,
        ];


        return $emailMap[$type] ?? null;
    }
}



