<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendServiceReminderJob;
use App\Models\CustomerVehicle;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Kirim notifikasi reminder servis berkala ke customer.
 *
 * Vehicle dianggap butuh reminder ketika tidak pernah diservis, atau servis
 * terakhirnya lebih lama dari interval (default 180 hari). Reminder yang
 * sudah terkirim (belum dibaca) tidak dikirim ulang.
 */
#[Signature('notifications:dispatch-service-reminders {--interval-days=180 : Interval servis berkala dalam hari} {--dry-run : Hanya tampilkan kendaraan yang akan di-reminder}')]
#[Description('Kirim reminder servis berkala ke customer berdasarkan riwayat servis kendaraan')]
class DispatchServiceReminders extends Command
{
    /**
     * Jalankan pengiriman reminder.
     */
    public function handle(): int
    {
        $intervalDays = max(1, (int) $this->option('interval-days'));
        $isDryRun = (bool) $this->option('dry-run');
        $cutoff = now()->subDays($intervalDays);

        $vehicles = CustomerVehicle::query()
            ->with(['customer.user'])
            ->whereHas('customer.user', fn ($query) => $query->where('is_active', true))
            ->get();

        $dueVehicles = $vehicles->filter(fn (CustomerVehicle $vehicle) => $this->isDueForReminder($vehicle, $cutoff));

        if ($isDryRun) {
            foreach ($dueVehicles as $vehicle) {
                $this->line($vehicle->customer->user->email.
                    ' | '.$vehicle->brand.' '.$vehicle->model.' ('.$vehicle->plate_number.')'.
                    ' | Servis terakhir: '.$this->lastServiceDate($vehicle)?->format('d M Y').' | BELUM DI-REMINDER');
            }

            $this->info('Dry-run: '.$dueVehicles->count().' kendaraan memenuhi syarat reminder.');

            return self::SUCCESS;
        }

        $dispatched = 0;
        foreach ($dueVehicles as $vehicle) {
            SendServiceReminderJob::dispatch($vehicle->id);
            $dispatched++;
        }

        Log::info('Service reminders dispatched', ['count' => $dispatched, 'interval_days' => $intervalDays]);
        $this->info("Reminder servis terkirim ke {$dispatched} kendaraan.");

        return self::SUCCESS;
    }

    /**
     * Vehicle butuh reminder jika belum pernah diservis, atau servis terakhir
     * sudah lewat dari cutoff, DAN tidak ada reminder yang belum dibaca.
     */
    protected function isDueForReminder(CustomerVehicle $vehicle, Carbon $cutoff): bool
    {
        $lastServiceDate = $this->lastServiceDate($vehicle);

        if ($lastServiceDate !== null && $lastServiceDate->gt($cutoff)) {
            return false;
        }

        return ! $this->hasUnreadReminder($vehicle);
    }

    /**
     * Tanggal servis terakhir yang masih berjalan / selesai (bukan cancel).
     */
    protected function lastServiceDate(CustomerVehicle $vehicle): ?Carbon
    {
        $latest = $vehicle->serviceBookings()
            ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
            ->orderByDesc('preferred_date')
            ->first();

        return $latest?->preferred_date ? Carbon::parse($latest->preferred_date) : null;
    }

    /**
     * Cek apakah customer sudah punya reminder servis yang belum dibaca untuk kendaraan ini.
     */
    protected function hasUnreadReminder(CustomerVehicle $vehicle): bool
    {
        $user = $vehicle->customer->user;

        if (! $user) {
            return false;
        }

        return $user->unreadNotifications()
            ->where('data->vehicle_id', $vehicle->id)
            ->whereIn('data->type', ['service_reminder', 'stnk_reminder', 'warranty_reminder'])
            ->exists();
    }
}
