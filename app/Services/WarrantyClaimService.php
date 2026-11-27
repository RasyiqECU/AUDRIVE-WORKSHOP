<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Enums\StockMovementType;
use App\Enums\WorkOrderStatus;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\WarrantyClaim;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Services\Notification\NotificationService;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class WarrantyClaimService
{
    /** Ketentuan garansi sesuai dokumen PDF: batas waktu dan jarak sejak serah terima. */
    public const SERVICE_DAYS = 30;

    public const SERVICE_KM = 1000;

    public const CAR_MONTHS = 6;

    public const CAR_KM = 5000;

    public function __construct(protected NotificationService $notificationService) {}

    /**
     * Cari transaksi dari nomor nota: kode Work Order, kode pesanan, atau nomor invoice.
     *
     * @return array{type: string, order: ?SalesOrder, workOrder: ?WorkOrder}|null
     */
    public function findTransaction(string $number): ?array
    {
        $number = trim($number);
        $invoice = Invoice::where('number', $number)->where('invoiceable_type', SalesOrder::class)->first();
        $order = $invoice?->invoiceable ?? SalesOrder::where('code', $number)->first();

        $workOrderCode = $number;
        if ($order?->type === SalesOrderType::Service && preg_match('/WO-[A-Z0-9-]+/', (string) $order->notes, $matches)) {
            $workOrderCode = $matches[0];
        }

        $workOrder = WorkOrder::where('code', $workOrderCode)->first();
        if ($workOrder) {
            return ['type' => 'service', 'order' => null, 'workOrder' => $workOrder];
        }

        if ($order?->type === SalesOrderType::CarSales) {
            return ['type' => 'car', 'order' => $order, 'workOrder' => null];
        }

        return null;
    }

    /** Tanggal mulai garansi, atau null bila transaksi belum lunas / belum diserahkan. */
    public function referenceDate(array $transaction): ?CarbonInterface
    {
        if ($transaction['type'] === 'service') {
            $workOrder = $transaction['workOrder'];

            return $workOrder->is_warranty ? null : $workOrder->delivered_at;
        }

        $order = $transaction['order'];

        return $order->status === SalesOrderStatus::Paid
            ? ($order->payments()->min('paid_at') ? now()->parse($order->payments()->min('paid_at')) : $order->updated_at)
            : null;
    }

    public function create(array $transaction, array $data, User $sales): WarrantyClaim
    {
        $isService = $transaction['type'] === 'service';
        $start = $this->referenceDate($transaction);

        if (! $start) {
            throw new \InvalidArgumentException('Transaksi ini belum lunas/diserahkan, atau merupakan pengerjaan garansi.');
        }

        $expiresAt = $isService ? $start->copy()->addDays(self::SERVICE_DAYS) : $start->copy()->addMonths(self::CAR_MONTHS);
        if (now()->greaterThan($expiresAt->endOfDay())) {
            throw new \InvalidArgumentException('Masa garansi sudah berakhir pada '.$expiresAt->format('d M Y').'.');
        }

        $item = null;
        if ($isService) {
            $workOrder = $transaction['workOrder'];
            $item = $workOrder->billableItems()->find($data['work_order_item_id'] ?? 0);

            if (! $item) {
                throw new \InvalidArgumentException('Pilih jasa/suku cadang yang diklaim.');
            }
            if ($item->part_source === 'customer') {
                throw new \InvalidArgumentException('Suku cadang bawaan customer tidak bergaransi.');
            }

            $referenceKm = $workOrder->serviceBooking?->odometer_km;
            $customerId = $workOrder->vehicle->customer_id;
            $vehicleId = $workOrder->customer_vehicle_id;
        } else {
            $order = $transaction['order'];
            $car = $order->items()->whereNotNull('car_unit_id')->first()?->carUnit;

            if (! $car) {
                throw new \InvalidArgumentException('Pesanan ini tidak memiliki unit mobil.');
            }
            if ($car->isConsignment()) {
                throw new \InvalidArgumentException('Mobil titip jual tidak ditanggung garansi AUDrive. Garansi mengikuti pernyataan penjual.');
            }

            $referenceKm = $car->mileage_km;
            $customerId = $order->customer_id;
            $vehicleId = null;
        }

        // Data lama tanpa catatan km: Sales mengisi dari nota
        $referenceKm ??= isset($data['reference_km']) ? (int) $data['reference_km'] : null;
        if ($referenceKm === null) {
            throw new \InvalidArgumentException('Isi kilometer saat serah terima sesuai nota.');
        }

        $currentKm = (int) $data['current_km'];
        $limit = $isService ? self::SERVICE_KM : self::CAR_KM;
        if ($currentKm < $referenceKm || $currentKm - $referenceKm > $limit) {
            throw new \InvalidArgumentException("Kilometer di luar batas garansi ({$referenceKm} s/d ".($referenceKm + $limit).' km).');
        }

        return DB::transaction(function () use ($transaction, $data, $sales, $isService, $item, $start, $referenceKm, $currentKm, $customerId, $vehicleId) {
            $column = $isService ? 'source_work_order_item_id' : 'source_sales_order_id';
            $sourceId = $isService ? $item->id : $transaction['order']->id;

            $hasOpenClaim = WarrantyClaim::where($column, $sourceId)
                ->where('status', '!=', 'rejected')
                ->where(fn ($q) => $q->whereNull('repair_work_order_id')
                    ->orWhereHas('repairWorkOrder', fn ($w) => $w->where('status', '!=', WorkOrderStatus::Delivered)))
                ->lockForUpdate()
                ->exists();

            if ($hasOpenClaim) {
                throw new \InvalidArgumentException('Masih ada klaim berjalan untuk item ini.');
            }

            if (! $vehicleId) {
                $car = $transaction['order']->items()->whereNotNull('car_unit_id')->first()->carUnit;
                $customer = $transaction['order']->customer;
                $vehicleId = $customer->vehicles()->firstOrCreate(
                    ['brand' => $car->brand, 'model' => $car->model, 'year' => $car->year],
                    ['color' => $car->color]
                )->id;
            }

            $claim = WarrantyClaim::create([
                'source_type' => $transaction['type'],
                'customer_id' => $customerId,
                'customer_vehicle_id' => $vehicleId,
                $column => $sourceId,
                'reference_date' => $start,
                'reference_km' => $referenceKm,
                'current_km' => $currentKm,
                'complaint' => $data['complaint'],
                'created_by' => $sales->id,
            ]);

            $this->notify($claim, ['mechanic'], 'Klaim garansi baru menunggu diagnosa.');

            return $claim;
        });
    }

    public function diagnose(WarrantyClaim $claim, User $mechanic, bool $isCovered, string $diagnosis): void
    {
        if ($claim->status !== 'submitted') {
            throw new \InvalidArgumentException('Klaim ini sudah didiagnosa.');
        }

        $claim->update([
            'status' => 'diagnosed',
            'is_covered' => $isCovered,
            'diagnosis' => $diagnosis,
            'diagnosed_by' => $mechanic->id,
        ]);

        $this->notify($claim, ['manager'], 'Diagnosa selesai, menunggu keputusan Anda.');
    }

    public function approve(WarrantyClaim $claim, User $manager): void
    {
        DB::transaction(function () use ($claim, $manager) {
            $claim = $this->lockDiagnosed($claim);

            $workOrder = WorkOrder::create([
                'customer_vehicle_id' => $claim->customer_vehicle_id,
                'status' => WorkOrderStatus::Queued,
                'assigned_mechanic_id' => $claim->diagnosed_by,
                'notes' => "Klaim garansi {$claim->code}: {$claim->complaint}",
                'is_warranty' => true,
            ]);

            WorkOrderItem::create([
                'work_order_id' => $workOrder->id,
                'type' => 'service',
                'description' => "Pengerjaan garansi {$claim->code}",
                'qty' => 1,
                'unit_price_idr' => 0,
                'subtotal_idr' => 0,
            ]);

            $claim->update([
                'status' => 'approved',
                'decided_by' => $manager->id,
                'decided_at' => now(),
                'repair_work_order_id' => $workOrder->id,
            ]);

            $this->notify($claim, ['inventory', 'sales'], 'Klaim disetujui. Siapkan suku cadang pengganti bila diperlukan.');
            $this->notifyCustomer($claim, 'Klaim garansi Anda disetujui dan akan dikerjakan tanpa biaya.');
        });
    }

    public function reject(WarrantyClaim $claim, User $manager, string $reason): void
    {
        DB::transaction(function () use ($claim, $manager, $reason) {
            $claim = $this->lockDiagnosed($claim);

            $claim->update([
                'status' => 'rejected',
                'decided_by' => $manager->id,
                'decided_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->notify($claim, ['sales'], "Klaim ditolak: {$reason}");
            $this->notifyCustomer($claim, "Klaim garansi Anda ditolak: {$reason}. Anda tetap bisa melanjutkan dengan servis berbayar.");
        });
    }

    /** Inventory mengeluarkan suku cadang pengganti (Rp 0, mutasi "Garansi"). */
    public function issuePart(WarrantyClaim $claim, SparePart $part, int $qty): void
    {
        $workOrder = $claim->repairWorkOrder;
        $open = [WorkOrderStatus::Queued, WorkOrderStatus::InProgress, WorkOrderStatus::WaitingPartConfirmation];

        if (! $workOrder || ! in_array($workOrder->status, $open, true)) {
            throw new \InvalidArgumentException('Suku cadang hanya bisa dikeluarkan saat perbaikan garansi belum masuk QC.');
        }

        DB::transaction(function () use ($claim, $workOrder, $part, $qty) {
            $locked = SparePart::whereKey($part->id)->lockForUpdate()->firstOrFail();

            if ($locked->available_stock < $qty) {
                throw new \InvalidArgumentException("Stok tidak cukup untuk {$locked->name}. Tersedia: {$locked->available_stock}");
            }

            $locked->decrement('stock', $qty);

            StockMovement::create([
                'spare_part_id' => $locked->id,
                'type' => StockMovementType::Warranty,
                'qty' => $qty,
                'reference_type' => WorkOrder::class,
                'reference_id' => $workOrder->id,
                'notes' => "Garansi {$claim->code}",
                'created_by' => auth()->id(),
            ]);

            WorkOrderItem::create([
                'work_order_id' => $workOrder->id,
                'type' => 'part',
                'spare_part_id' => $locked->id,
                'description' => "{$locked->name} (Garansi)",
                'qty' => $qty,
                'unit_price_idr' => 0,
                'subtotal_idr' => 0,
                'part_source' => 'warranty',
            ]);
        });
    }

    protected function lockDiagnosed(WarrantyClaim $claim): WarrantyClaim
    {
        $claim = WarrantyClaim::whereKey($claim->id)->lockForUpdate()->firstOrFail();

        if ($claim->status !== 'diagnosed') {
            throw new \InvalidArgumentException('Klaim belum didiagnosa atau sudah diputuskan.');
        }

        return $claim;
    }

    protected function notify(WarrantyClaim $claim, array $roles, string $message): void
    {
        $this->notificationService->notifyRoles($roles, 'warranty_claim', [
            'title' => "Klaim Garansi {$claim->code}",
            'message' => $message,
            'action_url' => route('admin.warranty-claims.show', $claim->id),
        ]);
    }

    protected function notifyCustomer(WarrantyClaim $claim, string $message): void
    {
        if ($user = $claim->customer?->user) {
            $this->notificationService->send($user, 'warranty_claim', [
                'title' => "Klaim Garansi {$claim->code}",
                'message' => $message,
                'action_url' => route('customer.warranty.index'),
            ]);
        }
    }
}
