<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\Customer;
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

    /**
     * Matriks relevansi notifikasi per tipe.
     *
     * Setiap tipe hanya boleh dikirim ke role yang memang berkepentingan.
     * Role di luar daftar (mis. mekanik menerima notifikasi invoice/pembayaran
     * customer) akan difilter otomatis oleh notifyRoles() dan dicatat sebagai
     * warning.
     *
     * - Daftar = superset audience yang sah untuk tipe tersebut.
     * - Array kosong []  = tipe khusus customer, tidak boleh ke staff.
     * - Tipe tidak terdaftar (null) = semua role diizinkan (backwards compatible).
     * Tambahkan tipe baru di sini ketika tipe notifikasi baru diperkenalkan.
     */
    protected array $roleAudienceMatrix = [
        // ── Bengkel / Workshop ──
        'service_booking_created' => ['mechanic', 'manager', 'cashier', 'super-admin'],
        'service_booking_confirmed' => ['mechanic', 'manager', 'cashier', 'sales', 'super-admin'],
        'booking_created' => ['mechanic', 'manager', 'cashier', 'super-admin'],
        'booking_confirmed' => ['mechanic', 'manager', 'cashier', 'super-admin'],
        'work_order_status_changed' => ['mechanic', 'cashier', 'manager', 'super-admin'],
        'repair_completed' => ['mechanic', 'cashier', 'manager', 'super-admin'],
        'work_order_delivered' => ['mechanic', 'cashier', 'manager', 'super-admin'],
        'ai_scan_completed' => ['mechanic', 'manager', 'super-admin'],

        // ── Showroom / Marketplace ──
        'test_drive_requested' => ['sales', 'manager', 'super-admin'],
        'test_drive_confirmed' => ['sales', 'manager', 'super-admin'],
        'test_drive_completed' => ['sales', 'manager', 'super-admin'],
        'offer_submitted' => ['sales', 'manager', 'super-admin'],
        'offer_accepted' => ['sales', 'manager', 'super-admin'],
        'offer_rejected' => ['sales', 'manager', 'super-admin'],

        // ── Transaksi, Pembayaran & Inventori ──
        'order_created' => ['cashier', 'inventory', 'manager', 'super-admin'],
        'payment_success' => ['cashier', 'sales', 'manager', 'super-admin'],
        'payment_failed' => ['cashier', 'manager', 'super-admin'],
        'low_stock_alert' => ['inventory', 'manager', 'super-admin'],

        // ── Tata Kelola ──
        'contact_message' => ['mechanic', 'sales', 'manager', 'super-admin'],
        'approval_requested' => ['manager', 'super-admin'],
        'approval_resolved' => ['manager', 'super-admin'],

        // ── Khusus Customer (reminder personal) — tidak boleh ke staff ──
        'service_reminder' => [],
        'stnk_reminder' => [],
        'warranty_reminder' => [],
    ];

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
            if (! isset($this->channels[$channel])) {
                Log::warning("Notification channel not found: {$channel}");

                return false;
            }

            $channelClass = $this->channels[$channel];
            $channelInstance = app($channelClass);

            return $channelInstance->send($user, $type, $data);

        } catch (\Exception $e) {
            Log::error('Notification send failed', [
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
     * Send notification to all active users with specific roles (e.g. ['sales', 'super-admin', 'manager'])
     *
     * Audience difilter melalui matriks relevansi $roleAudienceMatrix sehingga
     * role yang tidak berkepentingan dengan tipe notifikasi tidak pernah menerima.
     */
    public function notifyRoles(array $roles, string $type, array $data = []): array
    {
        $effectiveRoles = $this->resolveAllowedRoles($roles, $type);

        if (empty($effectiveRoles)) {
            return [];
        }

        $users = User::whereIn('role', $effectiveRoles)->where('is_active', true)->get();

        return $this->sendToMany($users->all(), $type, $data);
    }

    /**
     * Filter daftar role berdasarkan matriks relevansi tipe notifikasi.
     *
     * @param  array<int, string>  $roles
     * @return array<int, string> role yang benar-benar menerima notifikasi
     */
    protected function resolveAllowedRoles(array $roles, string $type): array
    {
        $requested = array_values(array_unique($roles));
        $allowed = $this->roleAudienceMatrix[$type] ?? null;

        if ($allowed === null) {
            return $requested;
        }

        $effective = array_values(array_intersect($requested, $allowed));
        $blocked = array_values(array_diff($requested, $effective));

        if (! empty($blocked)) {
            Log::warning('Notification audience filtered by relevance matrix', [
                'type' => $type,
                'blocked_roles' => $blocked,
                'allowed_roles' => $allowed,
            ]);
        }

        return $effective;
    }

    /**
     * Get the allowed role audience for a notification type (null = semua role).
     *
     * @return array<int, string>|null
     */
    public function allowedRolesFor(string $type): ?array
    {
        return $this->roleAudienceMatrix[$type] ?? null;
    }

    /**
     * Send notification to a customer (accepts Customer model or User model)
     */
    public function notifyCustomer(Customer|User $recipient, string $type, array $data = []): bool
    {
        $user = $recipient instanceof User ? $recipient : $recipient->user;
        if (! $user) {
            return false;
        }

        return $this->send($user, $type, $data);
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
        if (! isset($this->channels[$name])) {
            return null;
        }

        return app($this->channels[$name]);
    }
}
