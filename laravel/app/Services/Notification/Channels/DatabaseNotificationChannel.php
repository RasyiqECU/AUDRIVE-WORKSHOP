<?php

declare(strict_types=1);

namespace App\Services\Notification\Channels;

use App\Models\User;
use App\Services\Notification\Contracts\NotificationChannelContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DatabaseNotificationChannel implements NotificationChannelContract
{
    /**
     * Send notification via database
     */
    public function send(User $user, string $type, array $data = []): bool
    {
        try {
            $title = $data['title'] ?? $this->formatTitle($type, $data);
            $message = $data['message'] ?? $this->formatMessage($type, $data);

            $notificationData = array_merge($data, [
                'type'       => $type,
                'title'      => $title,
                'subject'    => $title,
                'message'    => $message,
                'action_url' => $data['action_url'] ?? null,
                'created_at' => now()->toIso8601String(),
            ]);

            $className = 'App\\Notifications\\' . $this->getNotificationClass($type);

            $user->notifications()->create([
                'id'      => Str::uuid()->toString(),
                'type'    => $className,
                'data'    => $notificationData,
                'read_at' => null,
            ]);

            Log::info("Database notification created", [
                'user_id' => $user->id,
                'type'    => $type,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Database notification failed", [
                'user_id' => $user->id,
                'type'    => $type,
                'error'   => $e->getMessage(),
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
     * Format title based on notification type
     */
    protected function formatTitle(string $type, array $data): string
    {
        return match ($type) {
            'contact_message'           => 'Pertanyaan Customer Baru',
            'contact_reply'             => 'Tanggapan dari Sales AUDrive',
            'test_drive_requested'      => 'Permohonan Uji Kendaraan (Test Drive)',
            'test_drive_confirmed'      => 'Jadwal Test Drive Dikonfirmasi',
            'test_drive_completed'      => 'Uji Kendaraan Selesai',
            'offer_submitted'           => 'Penawaran Harga Mobil Masuk',
            'offer_accepted'            => 'Penawaran Harga Disetujui',
            'offer_rejected'            => 'Penawaran Harga Ditolak',
            'service_booking_created'   => 'Booking Servis Baru',
            'service_booking_confirmed' => 'Booking Servis Dikonfirmasi',
            'work_order_status_changed' => 'Update Status Work Order',
            'repair_completed'          => 'Perbaikan Mobil Selesai',
            'work_order_delivered'      => 'Kendaraan Telah Diserahkan',
            'order_created'             => 'Pesanan Baru Masuk',
            'payment_success'           => 'Pembayaran Berhasil Diverifikasi',
            'payment_failed'            => 'Pembayaran Gagal',
            'low_stock_alert'           => 'Peringatan Stok Suku Cadang Menipis',
            'approval_requested'        => 'Pengajuan Approval Masuk',
            'approval_resolved'         => 'Hasil Pengajuan Approval',
            'ai_scan_completed'         => 'Hasil Scan AI Diagnostic Tersedia',
            default                     => 'Pemberitahuan Sistem AUDrive',
        };
    }

    /**
     * Format message based on notification type
     */
    protected function formatMessage(string $type, array $data): string
    {
        return match ($type) {
            'contact_message'           => "Pesan baru dari " . ($data['name'] ?? 'Customer') . " (" . ($data['topic'] ?? 'Umum') . "): " . Str::limit($data['preview'] ?? $data['message_text'] ?? '', 100),
            'contact_reply'             => "Sales AUDrive telah menanggapi pesan Anda: " . Str::limit($data['reply_text'] ?? '', 120),
            'test_drive_requested'      => "Permohonan Test Drive untuk unit " . ($data['car_name'] ?? 'Mobil') . " oleh " . ($data['customer_name'] ?? 'Customer') . " pada " . ($data['scheduled_at'] ?? 'waktu yang ditentukan'),
            'test_drive_confirmed'      => "Jadwal Test Drive unit " . ($data['car_name'] ?? 'Mobil') . " Anda telah disetujui untuk tanggal " . ($data['scheduled_at'] ?? ''),
            'test_drive_completed'      => "Sesi uji kendaraan unit " . ($data['car_name'] ?? 'Mobil') . " telah selesai dilaksanakan.",
            'offer_submitted'           => "Penawaran harga masuk untuk " . ($data['car_name'] ?? 'Mobil') . " senilai Rp " . number_format((int)($data['amount'] ?? 0), 0, ',', '.') . " dari " . ($data['customer_name'] ?? 'Customer'),
            'offer_accepted'            => "Selamat! Penawaran harga Anda untuk unit " . ($data['car_name'] ?? 'Mobil') . " telah DITERIMA oleh Sales AUDrive.",
            'offer_rejected'            => "Penawaran harga Anda untuk unit " . ($data['car_name'] ?? 'Mobil') . " belum dapat disetujui.",
            'booking_created', 'service_booking_created' => "Booking servis untuk kendaraan " . ($data['vehicle'] ?? 'Pelanggan') . " tanggal " . ($data['date'] ?? ''),
            'booking_confirmed', 'service_booking_confirmed' => "Booking servis Anda telah dikonfirmasi untuk tanggal " . ($data['date'] ?? ''),
            'repair_completed'          => "Mobil Anda (" . ($data['vehicle'] ?? 'Kendaraan') . ") telah SELESAI diservis/direpair (WO #" . ($data['work_order_code'] ?? '') . ") dan siap diambil.",
            'work_order_delivered'      => "Kendaraan Anda untuk WO #" . ($data['work_order_code'] ?? '') . " telah diserahkan. Terima kasih atas kunjungan Anda.",
            'work_order_status_changed' => "Status work order #" . ($data['work_order_code'] ?? '') . " telah berubah menjadi " . ($data['status'] ?? ''),
            'order_created'             => "Pesanan baru #" . ($data['order_code'] ?? '') . " berhasil dibuat senilai Rp " . number_format((int)($data['amount'] ?? 0), 0, ',', '.'),
            'payment_success'           => "Pembayaran sebesar Rp " . number_format((int)($data['amount'] ?? 0), 0, ',', '.') . " untuk pesanan #" . ($data['order_code'] ?? '') . " berhasil.",
            'payment_failed'            => "Pembayaran untuk pesanan #" . ($data['order_code'] ?? '') . " gagal diproses.",
            'ai_scan_completed'         => "Hasil scan AI Diagnostic kendaraan " . ($data['vehicle'] ?? '') . " telah selesai dianalisis.",
            'low_stock_alert'           => "Stok suku cadang " . ($data['part_name'] ?? '') . " menipis (" . ($data['remaining_stock'] ?? $data['current_stock'] ?? 0) . " pcs tersisa).",
            'approval_requested'        => "Pengajuan approval " . ($data['type'] ?? '') . " diajukan oleh " . ($data['requester_name'] ?? 'Staff') . " dan memerlukan verifikasi.",
            'approval_resolved'         => "Pengajuan approval #" . ($data['approval_id'] ?? '') . " Anda telah " . ($data['status'] ?? 'diproses') . ".",
            default                     => $data['message'] ?? "Terdapat aktivitas baru pada akun atau sistem Anda.",
        };
    }

    /**
     * Get notification class name
     */
    protected function getNotificationClass(string $type): string
    {
        $classMap = [
            'contact_message'           => 'ContactMessageNotification',
            'contact_reply'             => 'ContactReplyNotification',
            'test_drive_requested'      => 'TestDriveRequestedNotification',
            'test_drive_confirmed'      => 'TestDriveConfirmedNotification',
            'test_drive_completed'      => 'TestDriveCompletedNotification',
            'offer_submitted'           => 'OfferSubmittedNotification',
            'offer_accepted'            => 'OfferAcceptedNotification',
            'offer_rejected'            => 'OfferRejectedNotification',
            'booking_created'           => 'BookingCreatedNotification',
            'booking_confirmed'         => 'BookingConfirmedNotification',
            'service_booking_created'   => 'ServiceBookingCreatedNotification',
            'service_booking_confirmed' => 'ServiceBookingConfirmedNotification',
            'repair_completed'          => 'RepairCompletedNotification',
            'work_order_delivered'      => 'WorkOrderDeliveredNotification',
            'work_order_status_changed' => 'WorkOrderStatusChangedNotification',
            'order_created'             => 'OrderCreatedNotification',
            'payment_success'           => 'PaymentSuccessNotification',
            'payment_failed'            => 'PaymentFailedNotification',
            'ai_scan_completed'         => 'AiScanCompletedNotification',
            'low_stock_alert'           => 'LowStockAlertNotification',
            'approval_requested'        => 'ApprovalRequestedNotification',
            'approval_resolved'         => 'ApprovalResolvedNotification',
        ];

        return $classMap[$type] ?? 'GenericNotification';
    }
}
