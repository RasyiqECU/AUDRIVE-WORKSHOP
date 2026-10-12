<?php


declare(strict_types=1);


namespace App\Services\Notification\Channels;


use App\Models\User;
use App\Services\Notification\Contracts\NotificationChannelContract;
use Illuminate\Support\Facades\Log;


class DatabaseNotificationChannel implements NotificationChannelContract
{
    /**
     * Send notification via database
     */
    public function send(User $user, string $type, array $data = []): bool
    {
        try {
            $notificationData = [
                'type' => $type,
                'data' => $data,
                'message' => $this->formatMessage($type, $data),
                'created_at' => now(),
            ];


            $user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'type' => "App\\Notifications\\{this->getNotificationClass(type)}",
                'data' => $notificationData,
                'read_at' => null,
            ]);


            Log::info("Database notification created", [
                'user_id' => $user->id,
                'type' => $type,
            ]);


            return true;


        } catch (\Exception $e) {
            Log::error("Database notification failed", [
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
        return 'database';
    }


    /**
     * Format message based on notification type
     */
    protected function formatMessage(string $type, array $data): string
    {
        return match ($type) {
            'booking_created' => "Booking servis Anda telah dibuat untuk tanggal {$data['date']}",
            'booking_confirmed' => "Booking servis Anda telah dikonfirmasi",
            'work_order_status_changed' => "Status work order telah berubah menjadi {$data['status']}",
            'payment_success' => "Pembayaran sebesar Rp " . number_format($data['amount']) . " berhasil",
            'payment_failed' => "Pembayaran gagal. Silakan coba lagi",
            'offer_accepted' => "Penawaran Anda telah diterima",
            'offer_rejected' => "Penawaran Anda telah ditolak",
            'ai_scan_completed' => "Hasil scan AI telah tersedia",
            'low_stock_alert' => "Stok {$data['part_name']} rendah: {$data['current_stock']} unit",
            'service_reminder' => "Reminder: Servis berkala kendaraan Anda",
            'stnk_reminder' => "Reminder: STNK akan expired pada {$data['expiry_date']}",
            'warranty_reminder' => "Reminder: Garansi kendaraan akan expired pada {$data['expiry_date']}",
            default => "Notifikasi baru",
        };
    }


    /**
     * Get notification class name
     */
    protected function getNotificationClass(string $type): string
    {
        $classMap = [
            'booking_created' => 'BookingCreatedNotification',
            'booking_confirmed' => 'BookingConfirmedNotification',
            'work_order_status_changed' => 'WorkOrderStatusChangedNotification',
            'payment_success' => 'PaymentSuccessNotification',
            'payment_failed' => 'PaymentFailedNotification',
            'offer_accepted' => 'OfferAcceptedNotification',
            'offer_rejected' => 'OfferRejectedNotification',
            'ai_scan_completed' => 'AiScanCompletedNotification',
            'low_stock_alert' => 'LowStockAlertNotification',
            'service_reminder' => 'ServiceReminderNotification',
            'stnk_reminder' => 'StnkReminderNotification',
            'warranty_reminder' => 'WarrantyReminderNotification',
        ];


        return $classMap[$type] ?? 'GenericNotification';
    }
}



