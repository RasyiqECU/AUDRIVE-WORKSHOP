<?php


declare(strict_types=1);


namespace App\Services;


use App\Enums\StockMovementType;
use App\Enums\WorkOrderStatus;
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
        $vehicleName = $workOrder->vehicle ? ($workOrder->vehicle->brand . ' ' . $workOrder->vehicle->model . ' (' . $workOrder->vehicle->plate_number . ')') : 'Kendaraan Pelanggan';

        if ($status === WorkOrderStatus::Completed) {
            // Notifikasi ke Customer: Perbaikan Selesai
            if ($customerUser) {
                $this->notificationService->send($customerUser, 'repair_completed', [
                    'title'           => "Perbaikan Mobil Selesai: {$vehicleName}",
                    'message'         => "Perbaikan mobil {$vehicleName} (WO #{$workOrder->code}) telah SELESAI dan siap diambil di workshop AUDrive.",
                    'vehicle'         => $vehicleName,
                    'work_order_code' => $workOrder->code,
                    'action_url'      => route('workshop.tracking.show', $workOrder->id),
                ]);
            }

            // Notifikasi KHUSUS ke Kasir (Cashier): Tagihan Siap Diproses di POS
            $this->notificationService->notifyRoles(['cashier'], 'repair_completed', [
                'title'           => "Tagihan Siap Diproses (Kasir): WO #{$workOrder->code}",
                'message'         => "Pengerjaan servis WO #{$workOrder->code} ({$vehicleName}) telah SELESAI dikerjakan teknisi. Total tagihan: Rp " . number_format($workOrder->total_idr, 0, ',', '.') . ". Silakan proses pembayaran di kasir.",
                'vehicle'         => $vehicleName,
                'work_order_code' => $workOrder->code,
                'amount'          => $workOrder->total_idr,
                'action_url'      => route('admin.pos.index', ['work_order_id' => $workOrder->id]),
            ]);

            // Notifikasi ke Super Admin & Manager
            $this->notificationService->notifyRoles(['super-admin', 'manager'], 'repair_completed', [
                'title'           => "Work Order Selesai: #{$workOrder->code}",
                'message'         => "Pengerjaan repair WO #{$workOrder->code} ({$vehicleName}) telah selesai dikerjakan teknisi. Total billing: Rp " . number_format($workOrder->total_idr, 0, ',', '.'),
                'vehicle'         => $vehicleName,
                'work_order_code' => $workOrder->code,
                'amount'          => $workOrder->total_idr,
                'action_url'      => route('admin.work-orders.show', $workOrder->id),
            ]);
        } elseif ($status === WorkOrderStatus::Delivered) {
            if ($customerUser) {
                $this->notificationService->send($customerUser, 'work_order_delivered', [
                    'title'           => "Kendaraan Telah Diserahkan: {$vehicleName}",
                    'message'         => "Kendaraan {$vehicleName} (WO #{$workOrder->code}) telah resmi diserahkan. Terima kasih telah mempercayakan perawatan kendaraan Anda kepada AUDrive.",
                    'vehicle'         => $vehicleName,
                    'work_order_code' => $workOrder->code,
                    'action_url'      => route('workshop.tracking.show', $workOrder->id),
                ]);
            }

            // Notifikasi ke Mekanik yang bertugas
            if ($workOrder->mechanic) {
                $this->notificationService->send($workOrder->mechanic, 'work_order_delivered', [
                    'title'           => "Unit WO #{$workOrder->code} Telah Lunas & Diserahkan",
                    'message'         => "Kendaraan {$vehicleName} (WO #{$workOrder->code}) telah lunas dibayar di Kasir dan resmi diserahterimakan kepada pelanggan.",
                    'vehicle'         => $vehicleName,
                    'work_order_code' => $workOrder->code,
                    'action_url'      => route('admin.work-orders.show', $workOrder->id),
                ]);
            }
        } elseif ($status === WorkOrderStatus::InProgress) {
            if ($customerUser) {
                $this->notificationService->send($customerUser, 'work_order_status_changed', [
                    'title'           => "Servis Sedang Dikerjakan: {$vehicleName}",
                    'message'         => "Teknisi spesialis AUDrive telah mulai mengerjakan perbaikan pada kendaraan {$vehicleName} (WO #{$workOrder->code}).",
                    'vehicle'         => $vehicleName,
                    'work_order_code' => $workOrder->code,
                    'status'          => 'Sedang Dikerjakan',
                    'action_url'      => route('workshop.tracking.show', $workOrder->id),
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


    public function addPartItem(
        WorkOrder $workOrder,
        SparePart $sparePart,
        int $qty
    ): WorkOrderItem {
        // Validasi stok dengan lockForUpdate
        return DB::transaction(function () use ($workOrder, $sparePart, $qty) {
            // Lock spare part untuk mencegah race condition
            $lockedPart = SparePart::where('id', $sparePart->id)
                ->lockForUpdate()
                ->first();


            if ($lockedPart->stock < $qty) {
                throw new \InvalidArgumentException(
                    "Stok tidak cukup untuk {$lockedPart->name}. Tersedia: {$lockedPart->stock}, Diminta: {$qty}"
                );
            }


            // Kurangi stok
            $lockedPart->decrement('stock', $qty);


            // Buat stock movement
            StockMovement::create([
                'spare_part_id' => $sparePart->id,
                'type' => StockMovementType::Out,
                'qty' => $qty,
                'reference_type' => WorkOrder::class,
                'reference_id' => $workOrder->id,
                'notes' => "Digunakan di work order {$workOrder->code}",
                'created_by' => auth()->id(),
            ]);


            // Buat work order item
            $item = WorkOrderItem::create([
                'work_order_id' => $workOrder->id,
                'type' => 'part',
                'spare_part_id' => $sparePart->id,
                'description' => $sparePart->name,
                'qty' => $qty,
                'unit_price_idr' => $sparePart->price_idr,
                'subtotal_idr' => $qty * $sparePart->price_idr,
            ]);


            $this->recalculateTotals($workOrder);


            return $item;
        });
    }


    public function removeItem(WorkOrderItem $item): void
    {
        DB::transaction(function () use ($item) {
            $workOrder = $item->workOrder;


            // Jika item adalah part, kembalikan stok
            if ($item->type === 'part' && $item->spare_part_id) {
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


            $this->recalculateTotals($workOrder);
        });
    }


    public function complete(WorkOrder $workOrder): WorkOrder
    {
        // Validasi: work order harus memiliki minimal 1 item
        if ($workOrder->items()->count() === 0) {
            throw new \InvalidArgumentException('Work order harus memiliki minimal 1 item sebelum diselesaikan.');
        }


        // Validasi: total harus valid
        if ($workOrder->total_idr <= 0) {
            throw new \InvalidArgumentException('Total work order tidak valid.');
        }


        return DB::transaction(function () use ($workOrder) {
            // Update status work order
            $this->updateStatus($workOrder, WorkOrderStatus::Completed);


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


        return $this->updateStatus($workOrder, WorkOrderStatus::Delivered);
    }


    protected function validateStatusTransition(WorkOrderStatus $current, WorkOrderStatus $new): void
    {
        $transitions = [
            WorkOrderStatus::Booking->value => ['queued', 'in_progress', 'cancelled'],
            WorkOrderStatus::Queued->value => ['in_progress', 'cancelled'],
            WorkOrderStatus::InProgress->value => ['quality_control', 'completed', 'cancelled'],
            WorkOrderStatus::QualityControl->value => ['completed', 'in_progress'],
            WorkOrderStatus::Completed->value => ['delivered'],
            WorkOrderStatus::Delivered->value => [],
            WorkOrderStatus::Cancelled->value => [],
        ];


        $allowed = $transitions[$current->value] ?? [];


        if (!in_array($new->value, $allowed)) {
            throw new \InvalidArgumentException(
                "Transisi status dari {$current->label()} ke {$new->label()} tidak valid."
            );
        }
    }


    protected function recalculateTotals(WorkOrder $workOrder): void
    {
        $totalJasa = $workOrder->items()
            ->where('type', 'service')
            ->sum('subtotal_idr');


        $totalPart = $workOrder->items()
            ->where('type', 'part')
            ->sum('subtotal_idr');


        $workOrder->update([
            'total_jasa_idr' => $totalJasa,
            'total_part_idr' => $totalPart,
            'total_idr' => $totalJasa + $totalPart,
        ]);
    }
}



