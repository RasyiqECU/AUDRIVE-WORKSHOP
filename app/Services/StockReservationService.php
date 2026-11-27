<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Stok fisik = spare_parts.stock, dikunci sementara = booked_stock,
 * tersedia = stock - booked_stock.
 */
class StockReservationService
{
    public function __construct(protected NotificationService $notificationService) {}

    /**
     * Kunci stok. Satu UPDATE bersyarat per part sehingga checkout bersamaan
     * tidak bisa melebihi stok; part diproses urut id agar tidak deadlock.
     *
     * @param  array<int, int>  $quantities  [spare_part_id => qty]
     */
    public function reserve(array $quantities): void
    {
        ksort($quantities);

        DB::transaction(function () use ($quantities) {
            foreach ($quantities as $partId => $qty) {
                $reserved = SparePart::whereKey($partId)
                    ->whereRaw('stock - booked_stock >= ?', [$qty])
                    ->increment('booked_stock', $qty);

                if ($reserved === 0) {
                    $part = SparePart::find($partId);
                    throw new \InvalidArgumentException(
                        'Stok tidak cukup untuk '.($part?->name ?? 'suku cadang').'. Tersedia: '.max(0, $part?->available_stock ?? 0)
                    );
                }
            }
        });
    }

    /** Lepas kuncian (order kedaluwarsa/batal). Aman dipanggil ulang. */
    public function release(SalesOrder $order): void
    {
        if (! $this->claim($order)) {
            return;
        }

        foreach ($this->quantities($order) as $partId => $qty) {
            SparePart::withTrashed()->whereKey($partId)->decrement('booked_stock', $qty);
        }
    }

    /** Ubah kuncian menjadi pengurangan stok permanen (order lunas). */
    public function consume(SalesOrder $order, ?int $actorId = null): bool
    {
        if (! $this->claim($order)) {
            return false;
        }

        foreach ($this->quantities($order) as $partId => $qty) {
            SparePart::withTrashed()->whereKey($partId)->update([
                'stock' => DB::raw('stock - '.(int) $qty),
                'booked_stock' => DB::raw('booked_stock - '.(int) $qty),
            ]);

            StockMovement::create([
                'spare_part_id' => $partId,
                'type' => StockMovementType::Out,
                'qty' => $qty,
                'reference_type' => SalesOrder::class,
                'reference_id' => $order->id,
                'notes' => "Penjualan spare part - Order #{$order->code}",
                'created_by' => $actorId ?? $order->created_by,
            ]);

            $this->warnIfLow($partId);
        }

        return true;
    }

    /** Menandai kuncian sudah diproses; hanya satu pemanggil yang berhasil. */
    protected function claim(SalesOrder $order): bool
    {
        $claimed = SalesOrder::whereKey($order->id)->where('stock_reserved', true)->update(['stock_reserved' => false]);
        $order->stock_reserved = false;

        return $claimed === 1;
    }

    /** @return array<int, int> */
    protected function quantities(SalesOrder $order): array
    {
        return $order->items()->whereNotNull('spare_part_id')->get()
            ->groupBy('spare_part_id')
            ->map(fn ($items) => (int) $items->sum('qty'))
            ->sortKeys()
            ->all();
    }

    protected function warnIfLow(int $partId): void
    {
        $part = SparePart::find($partId);
        if (! $part || $part->stock > ($part->low_stock_threshold ?? 5)) {
            return;
        }

        try {
            $this->notificationService->notifyRoles(['inventory', 'super-admin'], 'low_stock_alert', [
                'title' => "Peringatan Stok Suku Cadang Menipis: {$part->name}",
                'message' => "Suku cadang {$part->name} tersisa {$part->stock} unit (Batas minimum: {$part->low_stock_threshold}). Segera lakukan restock.",
                'part_id' => $part->id,
                'action_url' => route('admin.spare-parts.show', $part->id),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim notifikasi low stock: '.$e->getMessage());
        }
    }
}
