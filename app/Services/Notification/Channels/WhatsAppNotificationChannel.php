<?php


declare(strict_types=1);


namespace App\Services\Notification\Channels;


use App\Models\User;
use App\Services\Notification\Contracts\NotificationChannelContract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class WhatsAppNotificationChannel implements NotificationChannelContract
{
    protected string $apiUrl;
    protected string $token;


    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url') ?? '';
        $this->token = config('services.whatsapp.token') ?? '';
    }


    /**
     * Send notification via WhatsApp
     */
    public function send(User $user, string $type, array $data = []): bool
    {
        try {
            $phone = $user->phone;


            if (!$phone) {
                Log::warning("User has no phone number", ['user_id' => $user->id]);
                return false;
            }


            $message = $this->formatMessage($type, $data);


            // In development, log instead of sending
            if (app()->environment('local', 'testing')) {
                Log::info("WhatsApp notification (fake)", [
                    'user_id' => $user->id,
                    'phone' => $phone,
                    'type' => $type,
                    'message' => $message,
                ]);
                return true;
            }


            // Send via WhatsApp API (Fonnte, Wablas, or similar)
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target' => $phone,
                'message' => $message,
            ]);


            if ($response->successful()) {
                Log::info("WhatsApp notification sent", [
                    'user_id' => $user->id,
                    'phone' => $phone,
                    'type' => $type,
                ]);
                return true;
            }


            throw new \Exception('WhatsApp API error: ' . $response->body());


        } catch (\Exception $e) {
            Log::error("WhatsApp notification failed", [
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
        return 'whatsapp';
    }


    /**
     * Format message for WhatsApp
     */
    protected function formatMessage(string $type, array $data): string
    {
        $template = match ($type) {
            'booking_created' => "🔧 *Booking Servis Dibuat*\n\nHalo {$data['customer_name']},\n\nBooking servis Anda telah dibuat:\n- Tanggal: {$data['date']}\n- Waktu: {$data['time']}\n- Kendaraan: {$data['vehicle']}\n\nTerima kasih telah menggunakan layanan AUDrive.",


            'booking_confirmed' => "✅ *Booking Dikonfirmasi*\n\nHalo {$data['customer_name']},\n\nBooking servis Anda telah dikonfirmasi.\n\nSampai jumpa di bengkel kami!",


            'work_order_status_changed' => "🔧 *Update Work Order*\n\nHalo {$data['customer_name']},\n\nStatus work order Anda telah berubah:\n- Kode: {$data['work_order_code']}\n- Status: {$data['status']}\n\nSilakan cek detail di aplikasi.",


            'payment_success' => "💰 *Pembayaran Berhasil*\n\nHalo {$data['customer_name']},\n\nPembayaran Anda sebesar *Rp " . number_format($data['amount']) . "* telah berhasil.\n\nTerima kasih!",


            'payment_failed' => "❌ *Pembayaran Gagal*\n\nHalo {$data['customer_name']},\n\nPembayaran Anda gagal. Silakan coba lagi atau hubungi customer service kami.",


            'offer_accepted' => "🎉 *Penawaran Diterima*\n\nHalo {$data['customer_name']},\n\nPenawaran Anda untuk {$data['car_name']} telah diterima.\n\nSilakan hubungi kami untuk proses selanjutnya.",


            'offer_rejected' => "😔 *Penawaran Ditolak*\n\nHalo {$data['customer_name']},\n\nMohon maaf, penawaran Anda untuk {$data['car_name']} belum dapat kami terima.\n\nSilakan buat penawaran baru.",


            'ai_scan_completed' => "🔊 *Hasil Scan AI Tersedia*\n\nHalo {$data['customer_name']},\n\nHasil analisis suara mesin Anda telah tersedia.\n\nSilakan cek di aplikasi untuk melihat detail.",


            'low_stock_alert' => "⚠️ *Alert Stok Rendah*\n\nStok *{$data['part_name']}* rendah.\n\n- Stok saat ini: {$data['current_stock']} unit\n- Threshold: {$data['threshold']} unit\n\nSegera lakukan restock.",


            'service_reminder' => "🔔 *Reminder Servis*\n\nHalo {$data['customer_name']},\n\nKendaraan *{$data['vehicle']}* Anda sudah waktunya untuk servis berkala.\n\nSegera booking servis Anda di AUDrive!",


            'stnk_reminder' => "📋 *Reminder STNK*\n\nHalo {$data['customer_name']},\n\nSTNK kendaraan *{$data['vehicle']}* akan expired pada *{$data['expiry_date']}*.\n\nSegera perpanjang STNK Anda.",


            'warranty_reminder' => "🛡️ *Reminder Garansi*\n\nHalo {$data['customer_name']},\n\nGaransi kendaraan *{$data['vehicle']}* akan expired pada *{$data['expiry_date']}*.\n\nManfaatkan garansi Anda sebelum expired.",


            default => "Notifikasi dari AUDrive",
        };


        return $template;
    }
}



