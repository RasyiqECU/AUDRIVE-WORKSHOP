<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CarUnitStatus;
use App\Enums\SalesOrderStatus;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;

/** Titip jual: mobil tetap milik customer sampai laku. */
class ConsignmentService
{
    public function __construct(
        protected CarUnitService $carUnitService,
        protected NotificationService $notificationService
    ) {}

    public function submit(Customer $seller, array $data, array $photos = []): CarUnit
    {
        $car = DB::transaction(function () use ($seller, $data, $photos) {
            $car = $this->carUnitService->create($data + [
                'condition' => 'used',
                'ownership' => 'consignment',
                'seller_customer_id' => $seller->id,
                'price_sell_idr' => $data['asking_price_idr'],
                'status' => CarUnitStatus::InInspection,
            ]);

            if ($photos !== []) {
                $this->carUnitService->uploadPhotos($car, $photos);
            }

            return $car;
        });

        $this->notificationService->notifyRoles(['sales'], 'consignment', [
            'title' => "Pengajuan Titip Jual: {$car->full_name}",
            'message' => "{$seller->user?->name} mengajukan titip jual. Jadwalkan inspeksi.",
            'action_url' => route('admin.cars.show', $car),
        ]);

        return $car;
    }

    public function approve(CarUnit $car, array $data): void
    {
        $this->assertStatus($car, [CarUnitStatus::InInspection]);

        $car->update($data + ['status' => CarUnitStatus::Available]);

        $this->notifySeller($car, "{$car->full_name} sudah tayang di marketplace.");
    }

    public function reject(CarUnit $car, string $reason): void
    {
        $this->assertStatus($car, [CarUnitStatus::InInspection]);

        $car->update(['status' => CarUnitStatus::Inactive]);

        $this->notifySeller($car, "Pengajuan titip jual {$car->full_name} belum bisa diterima: {$reason}");
    }

    /** Penjual hanya bisa menarik unit sebelum dipesan. */
    public function withdraw(CarUnit $car): void
    {
        $this->assertStatus($car, [CarUnitStatus::InInspection, CarUnitStatus::Available]);

        $car->update(['status' => CarUnitStatus::Inactive]);
    }

    /** Kasir mencatat pembayaran ke penjual setelah unit lunas terjual. */
    public function paySeller(CarUnit $car, User $cashier): int
    {
        return DB::transaction(function () use ($car, $cashier) {
            $car = CarUnit::whereKey($car->id)->lockForUpdate()->firstOrFail();

            $sale = $car->salesOrderItems()
                ->whereHas('salesOrder', fn ($q) => $q->where('status', SalesOrderStatus::Paid))
                ->with('salesOrder')
                ->latest()
                ->first();

            if (! $car->isConsignment() || $car->status !== CarUnitStatus::Sold || ! $sale || $car->seller_paid_at) {
                throw new \InvalidArgumentException('Unit ini tidak menunggu pembayaran ke penjual.');
            }

            $payout = max(0, $sale->salesOrder->total_idr - (int) $car->commission_idr);
            $car->update(['seller_payout_idr' => $payout, 'seller_paid_at' => now(), 'seller_paid_by' => $cashier->id]);

            $this->notifySeller($car, "Hasil penjualan {$car->full_name} sebesar Rp ".number_format($payout, 0, ',', '.').' sudah dibayarkan.');

            return $payout;
        });
    }

    protected function assertStatus(CarUnit $car, array $statuses): void
    {
        if (! $car->isConsignment() || ! in_array($car->status, $statuses, true)) {
            throw new \InvalidArgumentException('Unit titip jual ini tidak bisa diproses pada status sekarang.');
        }
    }

    protected function notifySeller(CarUnit $car, string $message): void
    {
        if ($user = $car->seller?->user) {
            $this->notificationService->send($user, 'consignment', [
                'title' => 'Titip Jual Mobil',
                'message' => $message,
                'action_url' => route('marketplace.sell.create'),
            ]);
        }
    }
}
