<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StockMovementType;
use App\Enums\WorkOrderStatus;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
    public function __construct(
        protected ?AuditService $auditService = null,
        protected ?NotificationService $notificationService = null
    ) {
        $this->auditService = $auditService ?? app(AuditService::class);
        $this->notificationService = $notificationService ?? app(NotificationService::class);
    }

    public function updateStatus(WorkOrder $workOrder, WorkOrderStatus $status): WorkOrder
    {
        // Validasi transisi status
        $this->validateStatusTransition($workOrder->status, $status);

        if (in_array($status, [WorkOrderStatus::QualityControl, WorkOrderStatus::Completed], true) && $workOrder->hasPendingParts()) {
            throw new \InvalidArgumentException('Masih ada suku cadang yang menunggu konfirmasi customer.');
        }

        $oldStatus = $workOrder->status;
        $workOrder->update(['status' => $status]);

        // Jika status berubah menjadi completed, update timestamp
        if ($status === WorkOrderStatus::Completed) {
            $workOrder->update(['completed_at' => now()]);
        }

        // Log perubahan status
        $this->auditService->log(
            action: 'status_changed',
            modelType: 'work_order',
            modelId: $workOrder->id,
            oldValues: ['status' => $oldStatus->value],
            newValues: ['status' => $status->value],
            user: auth()->user(),
            description: "Work Order #{$workOrder->code} status changed from {$oldStatus->value} to {$status->value}"
        );

        // Notifikasi ke Customer & Role Terkait
        $customerUser = $workOrder->vehicle?->customer?->user ?? $workOrder->serviceBooking?->customer?->user;
        $vehicleName = $workOrder->vehicle ? ($workOrder->vehicle->brand.' '.$workOrder->vehicle->model.' ('.$workOrder->vehicle->plate_number.')') : 'Kendaraan Pelanggan';

        if ($status === WorkOrderStatus::Completed) {
            // Notifikasi ke Customer: Perbaikan Selesai
            if ($customerUser) {
                $this->notificationService->send($customerUser, 'repair_completed', [
                    'title' => "Perbaikan Mobil Selesai: {$vehicleName}",
                    'message' => "Perbaikan mobil {$vehicleName} (WO #{$workOrder->code}) telah SELESAI dan siap diambil di workshop AUDrive.",
                    'vehicle' => $vehicleName,
                    'work_order_code' => $workOrder->code,
                    'action_url' => route('workshop.tracking.show', $workOrder->id),
                ]);
            }

            // Notifikasi KHUSUS ke Kasir (Cashier): Tagihan Siap Diproses di POS
            $this->notificationService->notifyRoles(['cashier'], 'repair_completed', [
                'title' => "Tagihan Siap Diproses (Kasir): WO #{$workOrder->code}",
                'message' => "Pengerjaan servis WO #{$workOrder->code} ({$vehicleName}) telah SELESAI dikerjakan teknisi. Total tagihan: Rp ".number_format($workOrder->total_idr, 0, ',', '.').'. Silakan proses pembayaran di kasir.',
                'vehicle' => $vehicleName,
                'work_order_code' => $workOrder->code,
                'amount' => $workOrder->total_idr,
                'action_url' => route('admin.pos.index', ['work_order_id' => $workOrder->id]),
            ]);

            // Notifikasi ke Super Admin & Manager
            $this->notificationService->notifyRoles(['super-admin'], 'repair_completed', [
                'title' => "Work Order Selesai: #{$workOrder->code}",
                'message' => "Pengerjaan repair WO #{$workOrder->code} ({$vehicleName}) telah selesai dikerjakan teknisi. Total billing: Rp ".number_format($workOrder->total_idr, 0, ',', '.'),
                'vehicle' => $vehicleName,
                'work_order_code' => $workOrder->code,
                'amount' => $workOrder->total_idr,
                'action_url' => route('admin.work-orders.show', $workOrder->id),
            ]);
        } elseif ($status === WorkOrderStatus::Delivered) {
            if ($customerUser) {
                $this->notificationService->send($customerUser, 'work_order_delivered', [
                    'title' => "Kendaraan Telah Diserahkan: {$vehicleName}",
                    'message' => "Kendaraan {$vehicleName} (WO #{$workOrder->code}) telah resmi diserahkan. Terima kasih telah mempercayakan perawatan kendaraan Anda kepada AUDrive.",
                    'vehicle' => $vehicleName,
                    'work_order_code' => $workOrder->code,
                    'action_url' => route('workshop.tracking.show', $workOrder->id),
                ]);
            }

            // Notifikasi ke Kepala Mekanik yang bertugas
            if ($workOrder->mechanic) {
                $this->notificationService->send($workOrder->mechanic, 'work_order_delivered', [
                    'title' => "Unit WO #{$workOrder->code} Telah Lunas & Diserahkan",
                    'message' => "Kendaraan {$vehicleName} (WO #{$workOrder->code}) telah lunas dibayar di Kasir dan resmi diserahterimakan kepada pelanggan.",
                    'vehicle' => $vehicleName,
                    'work_order_code' => $workOrder->code,
                    'action_url' => route('admin.work-orders.show', $workOrder->id),
                ]);
            }
        } elseif ($status === WorkOrderStatus::InProgress) {
            if ($customerUser) {
                $this->notificationService->send($customerUser, 'work_order_status_changed', [
                    'title' => "Servis Sedang Dikerjakan: {$vehicleName}",
                    'message' => "Teknisi spesialis AUDrive telah mulai mengerjakan perbaikan pada kendaraan {$vehicleName} (WO #{$workOrder->code}).",
                    'vehicle' => $vehicleName,
                    'work_order_code' => $workOrder->code,
                    'status' => 'Sedang Dikerjakan',
                    'action_url' => route('workshop.tracking.show', $workOrder->id),
                ]);
            }
        }

        return $workOrder;
    }

    public function addServiceItem(
        WorkOrder $workOrder,
        string $description,
        int $qty,
        int $unitPrice
    ): WorkOrderItem {
        // Pengerjaan garansi tidak ditagihkan
        $unitPrice = $workOrder->is_warranty ? 0 : $unitPrice;

        return DB::transaction(function () use ($workOrder, $description, $qty, $unitPrice) {
            $item = WorkOrderItem::create([
                'work_order_id' => $workOrder->id,
                'type' => 'service',
                'description' => $description,
                'qty' => $qty,
                'unit_price_idr' => $unitPrice,
                'subtotal_idr' => $qty * $unitPrice,
            ]);

            $this->recalculateTotals($workOrder);

            return $item;
        });
    }

    /**
     * Usulan suku cadang: belum memotong stok dan belum masuk tagihan
     * sampai customer menyetujui (dicatat Sales).
     */
    public function addPartItem(
        WorkOrder $workOrder,
        SparePart $sparePart,
        int $qty
    ): WorkOrderItem {
        if ($workOrder->is_warranty) {
            throw new \InvalidArgumentException('Suku cadang garansi dikeluarkan Inventory lewat halaman klaim garansi.');
        }

        $item = WorkOrderItem::create([
            'work_order_id' => $workOrder->id,
            'type' => 'part',
            'spare_part_id' => $sparePart->id,
            'description' => $sparePart->name,
            'qty' => $qty,
            'unit_price_idr' => $sparePart->price_idr,
            'subtotal_idr' => $qty * $sparePart->price_idr,
            'approval_status' => 'pending',
        ]);

        $this->syncPartWaitingStatus($workOrder);

        $this->notificationService->notifyRoles(['inventory'], 'part_request', [
            'title' => "Usulan Suku Cadang: WO #{$workOrder->code}",
            'message' => "{$sparePart->name} x{$qty} menunggu pengecekan stok dan harga.",
            'action_url' => route('admin.part-requests.index'),
        ]);

        return $item;
    }

    public function markPartChecked(WorkOrderItem $item, ?int $installFee = null): void
    {
        $this->assertPending($item);

        $item->update(['inventory_checked_at' => now(), 'install_fee_idr' => $installFee]);

        $workOrder = $item->workOrder;
        if ($customerUser = $workOrder->vehicle?->customer?->user) {
            $this->notificationService->send($customerUser, 'part_request', [
                'title' => "Konfirmasi Suku Cadang: WO #{$workOrder->code}",
                'message' => "{$item->description}: Rp ".number_format($item->subtotal_idr, 0, ',', '.').'. Pilih beli di bengkel, bawa sendiri, atau tidak diganti.',
                'action_url' => route('workshop.tracking.show', $workOrder->id),
            ]);
        }

        $this->notificationService->notifyRoles(['sales'], 'part_request', [
            'title' => "Konfirmasi Customer: WO #{$item->workOrder->code}",
            'message' => "{$item->description} sudah dicek gudang. Kirim rincian biaya ke customer.",
            'action_url' => route('admin.part-requests.index'),
        ]);
    }

    /**
     * Catat pilihan customer: beli di bengkel, bawa sendiri, atau tidak diganti.
     */
    public function decidePart(
        WorkOrderItem $item,
        string $decision,
        int $installFee = 0,
        ?string $note = null
    ): void {
        DB::transaction(function () use ($item, $decision, $installFee, $note) {
            $item = WorkOrderItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $this->assertPending($item);

            if (! $item->inventory_checked_at) {
                throw new \InvalidArgumentException('Gudang belum mengecek stok dan harga item ini.');
            }

            $workOrder = $item->workOrder;
            $changes = ['decided_by' => auth()->id(), 'decided_at' => now(), 'decision_note' => $note];

            if ($decision === 'buy') {
                $part = SparePart::whereKey($item->spare_part_id)->lockForUpdate()->firstOrFail();

                if ($part->available_stock < $item->qty) {
                    throw new \InvalidArgumentException(
                        "Stok tidak cukup untuk {$part->name}. Tersedia: {$part->available_stock}, Diminta: {$item->qty}. Tunggu restock, atau pilih bawa sendiri / tidak diganti."
                    );
                }

                $part->decrement('stock', $item->qty);

                StockMovement::create([
                    'spare_part_id' => $part->id,
                    'type' => StockMovementType::Out,
                    'qty' => $item->qty,
                    'reference_type' => WorkOrder::class,
                    'reference_id' => $workOrder->id,
                    'notes' => "Digunakan di work order {$workOrder->code}",
                    'created_by' => auth()->id(),
                ]);

                $changes += ['approval_status' => 'approved', 'part_source' => 'workshop'];
            } elseif ($decision === 'bring_own') {
                // Hanya jasa pasang yang ditagih; part bawaan customer tidak bergaransi
                $changes += [
                    'approval_status' => 'approved',
                    'part_source' => 'customer',
                    'type' => 'service',
                    'description' => "Jasa pasang (part bawaan customer): {$item->description} x{$item->qty}",
                    'spare_part_id' => null,
                    'qty' => 1,
                    'unit_price_idr' => $installFee,
                    'subtotal_idr' => $installFee,
                ];
            } else {
                $changes += ['approval_status' => 'rejected'];
            }

            $item->update($changes);
            $this->recalculateTotals($workOrder);
            $this->syncPartWaitingStatus($workOrder);

            if ($workOrder->mechanic) {
                $this->notificationService->send($workOrder->mechanic, 'part_request', [
                    'title' => "Keputusan Suku Cadang: WO #{$workOrder->code}",
                    'message' => $decision === 'skip'
                        ? "Customer tidak mengganti {$item->description}. Lanjutkan sisa pekerjaan."
                        : "Customer menyetujui: {$item->description}.",
                    'action_url' => route('admin.work-orders.show', $workOrder->id),
                ]);
            }
        });
    }

    /** Dikerjakan ⇄ Menunggu Konfirmasi Part, mengikuti ada tidaknya usulan. */
    protected function syncPartWaitingStatus(WorkOrder $workOrder): void
    {
        $pending = $workOrder->hasPendingParts();

        if ($pending && $workOrder->status === WorkOrderStatus::InProgress) {
            $workOrder->update(['status' => WorkOrderStatus::WaitingPartConfirmation]);
        } elseif (! $pending && $workOrder->status === WorkOrderStatus::WaitingPartConfirmation) {
            $workOrder->update(['status' => WorkOrderStatus::InProgress]);
        }
    }

    protected function assertPending(WorkOrderItem $item): void
    {
        $closed = [WorkOrderStatus::Completed, WorkOrderStatus::Delivered, WorkOrderStatus::Cancelled];

        if ($item->approval_status !== 'pending' || in_array($item->workOrder->status, $closed, true)) {
            throw new \InvalidArgumentException('Item ini tidak sedang menunggu konfirmasi.');
        }
    }

    public function removeItem(WorkOrderItem $item): void
    {
        DB::transaction(function () use ($item) {
            $workOrder = $item->workOrder;

            // Jika item adalah part yang stoknya sudah dipotong, kembalikan stok
            if ($item->type === 'part' && $item->spare_part_id && $item->approval_status === 'approved') {
                $sparePart = SparePart::where('id', $item->spare_part_id)
                    ->lockForUpdate()
                    ->first();

                // Tambahkan kembali stok
                $sparePart->increment('stock', $item->qty);

                // Buat stock movement untuk reversal
                StockMovement::create([
                    'spare_part_id' => $sparePart->id,
                    'type' => StockMovementType::In,
                    'qty' => $item->qty,
                    'reference_type' => WorkOrder::class,
                    'reference_id' => $workOrder->id,
                    'notes' => "Reversal dari work order {$workOrder->code}",
                    'created_by' => auth()->id(),
                ]);
            }

            $item->delete();
            $this->syncPartWaitingStatus($workOrder);

            $this->recalculateTotals($workOrder);
        });
    }

    /** Lolos QC oleh Kepala Mekanik → servis selesai, menunggu pembayaran. */
    public function complete(WorkOrder $workOrder): WorkOrder
    {
        // Validasi: work order harus memiliki minimal 1 item
        if ($workOrder->billableItems()->count() === 0) {
            throw new \InvalidArgumentException('Work order harus memiliki minimal 1 item sebelum diselesaikan.');
        }

        // Validasi: total harus valid
        if ($workOrder->total_idr <= 0 && ! $workOrder->is_warranty) {
            throw new \InvalidArgumentException('Total work order tidak valid.');
        }

        return DB::transaction(function () use ($workOrder) {
            // Update status work order
            $this->updateStatus($workOrder, WorkOrderStatus::Completed);

            // Tagihan final terbit; customer bisa bayar di kasir atau transfer
            app(ServiceBillingService::class)->issue($workOrder, auth()->id());

            // Update booking terkait jika ada
            if ($workOrder->serviceBooking) {
                $workOrder->serviceBooking->update(['status' => 'completed']);
            }

            return $workOrder;
        });
    }

    public function deliver(WorkOrder $workOrder): WorkOrder
    {
        if ($workOrder->status !== WorkOrderStatus::Completed) {
            throw new \InvalidArgumentException('Hanya work order completed yang bisa diserahkan.');
        }

        if (! $workOrder->paid_at) {
            throw new \InvalidArgumentException('Kendaraan belum lunas. Proses pembayaran di kasir terlebih dahulu.');
        }

        $this->updateStatus($workOrder, WorkOrderStatus::Delivered);
        $workOrder->update(['delivered_at' => now()]);

        // Sales yang menyerahkan kendaraan menjadi penanggung jawab omzetnya
        SalesOrder::where('work_order_id', $workOrder->id)->get()
            ->each(fn (SalesOrder $order) => $order->assignSales(auth()->user()));

        return $workOrder;
    }

    protected function validateStatusTransition(WorkOrderStatus $current, WorkOrderStatus $new): void
    {
        $transitions = [
            WorkOrderStatus::Booking->value => ['queued', 'in_progress', 'cancelled'],
            WorkOrderStatus::Queued->value => ['in_progress', 'cancelled'],
            WorkOrderStatus::InProgress->value => ['quality_control', 'waiting_part_confirmation', 'cancelled'],
            WorkOrderStatus::WaitingPartConfirmation->value => ['in_progress', 'cancelled'],
            WorkOrderStatus::QualityControl->value => ['completed', 'in_progress'],
            WorkOrderStatus::Completed->value => ['delivered'],
            WorkOrderStatus::Delivered->value => [],
            WorkOrderStatus::Cancelled->value => [],
        ];

        $allowed = $transitions[$current->value] ?? [];

        if (! in_array($new->value, $allowed)) {
            throw new \InvalidArgumentException(
                "Transisi status dari {$current->label()} ke {$new->label()} tidak valid."
            );
        }
    }

    protected function recalculateTotals(WorkOrder $workOrder): void
    {
        $totalJasa = $workOrder->billableItems()
            ->where('type', 'service')
            ->sum('subtotal_idr');

        $totalPart = $workOrder->billableItems()
            ->where('type', 'part')
            ->sum('subtotal_idr');

        $workOrder->update([
            'total_jasa_idr' => $totalJasa,
            'total_part_idr' => $totalPart,
            'total_idr' => $totalJasa + $totalPart,
        ]);
    }
}
