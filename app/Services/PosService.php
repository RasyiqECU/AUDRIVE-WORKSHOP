<?php


declare(strict_types=1);


namespace App\Services;


use App\Enums\CarUnitStatus;
use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Enums\StockMovementType;
use App\Enums\WorkOrderStatus;
use App\Models\AuditLog;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Models\WorkOrder;
use App\Services\Notification\Contracts\NotificationServiceContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PosService
{
    public function createPosTransaction(
        array $items,
        ?Customer $customer,
        int $discountIdr,
        string $paymentMethod,
        int $userId
    ): SalesOrder {
        return DB::transaction(function () use ($items, $customer, $discountIdr, $paymentMethod, $userId) {
            // Validate stock for all items
            foreach ($items as $item) {
                $part = SparePart::where('id', $item['spare_part_id'])
                    ->lockForUpdate()
                    ->first();


                if (!$part) {
                    throw new \Exception("Spare part tidak ditemukan.");
                }


                if ($part->stock < $item['qty']) {
                    throw new \Exception("Stok tidak cukup untuk {$part->name}. Tersedia: {$part->stock}");
                }
            }


            // Calculate subtotal
            $subtotal = 0;
            foreach ($items as $item) {
                $part = SparePart::find($item['spare_part_id']);
                $subtotal += $part->price_idr * $item['qty'];
            }


            // Calculate tax and total
            $tax = (int) (($subtotal - $discountIdr) * 0.11); // 11% tax
            $total = $subtotal - $discountIdr + $tax;


            // Create sales order
            $salesOrder = SalesOrder::create([
                'code' => $this->generateOrderCode(),
                'customer_id' => $customer?->id,
                'type' => SalesOrderType::Pos,
                'status' => SalesOrderStatus::Paid,
                'subtotal_idr' => $subtotal,
                'discount_idr' => $discountIdr,
                'tax_idr' => $tax,
                'total_idr' => $total,
                'paid_idr' => $total,
                'payment_status' => PaymentStatus::Success,
                'notes' => "POS Transaction - Payment: {$paymentMethod}",
                'created_by' => $userId,
            ]);


            // Create sales order items and reduce stock
            foreach ($items as $item) {
                $part = SparePart::where('id', $item['spare_part_id'])
                    ->lockForUpdate()
                    ->first();


                // Create sales order item
                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_type' => 'spare_part',
                    'spare_part_id' => $part->id,
                    'description' => $part->name,
                    'qty' => $item['qty'],
                    'unit_price_idr' => $part->price_idr,
                    'subtotal_idr' => $part->price_idr * $item['qty'],
                ]);


                // Reduce stock
                $part->decrement('stock', $item['qty']);


                // Create stock movement
                StockMovement::create([
                    'spare_part_id' => $part->id,
                    'type' => StockMovementType::Out,
                    'qty' => $item['qty'],
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $salesOrder->id,
                    'notes' => "POS Transaction - Order #{$salesOrder->code}",
                    'created_by' => $userId,
                ]);
            }


            // Create payment
            Payment::create([
                'paymentable_type' => SalesOrder::class,
                'paymentable_id' => $salesOrder->id,
                'method' => $paymentMethod,
                'status' => PaymentStatus::Success,
                'amount_idr' => $total,
                'reference' => 'POS-' . strtoupper(Str::random(10)),
                'paid_at' => now(),
            ]);


            // Create invoice
            Invoice::create([
                'invoiceable_type' => SalesOrder::class,
                'invoiceable_id' => $salesOrder->id,
                'number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'status' => 'paid',
                'due_at' => now(),
                'total_idr' => $total,
            ]);


            // Add loyalty points if customer is logged in
            if ($customer) {
                $points = (int) (($subtotal - $discountIdr) / 10000); // 1 point per Rp 10.000
                if ($points > 0) {
                    $customer->increment('loyalty_points', $points);


                    $customer->loyaltyTransactions()->create([
                        'type' => 'earning',
                        'points' => $points,
                        'reference_type' => SalesOrder::class,
                        'reference_id' => $salesOrder->id,
                        'description' => "Poin dari transaksi POS #{$salesOrder->code}",
                    ]);
                }
            }


            return $salesOrder;
        });
    }


    public function createWorkOrderPayment(
        WorkOrder $workOrder,
        int $discountIdr,
        string $paymentMethod,
        int $userId
    ): SalesOrder {
        return DB::transaction(function () use ($workOrder, $discountIdr, $paymentMethod, $userId) {
            $workOrder->load(['items.sparePart', 'vehicle.customer', 'serviceBooking.customer']);

            if ($workOrder->total_idr <= 0) {
                throw new \InvalidArgumentException("Total Work Order harus lebih besar dari 0.");
            }

            // Cek apakah transaksi sudah pernah dibuat
            $existingOrder = SalesOrder::where('notes', 'like', "%{$workOrder->code}%")
                ->where('type', SalesOrderType::Service)
                ->where('status', SalesOrderStatus::Paid)
                ->first();

            if ($existingOrder) {
                return $existingOrder;
            }

            $customer = $workOrder->vehicle?->customer ?? $workOrder->serviceBooking?->customer;
            $subtotal = $workOrder->total_idr;
            $discount = min($discountIdr, $subtotal);
            $tax = (int) (($subtotal - $discount) * 0.11);
            $total = $subtotal - $discount + $tax;

            // Buat Sales Order
            $salesOrder = SalesOrder::create([
                'code' => $this->generateOrderCode(),
                'customer_id' => $customer?->id,
                'type' => SalesOrderType::Service,
                'status' => SalesOrderStatus::Paid,
                'subtotal_idr' => $subtotal,
                'discount_idr' => $discount,
                'tax_idr' => $tax,
                'total_idr' => $total,
                'paid_idr' => $total,
                'payment_status' => PaymentStatus::Success,
                'notes' => "Pembayaran Work Order #{$workOrder->code} - Metode: " . strtoupper($paymentMethod),
                'created_by' => $userId,
            ]);

            // Salin item WO ke SalesOrderItem
            foreach ($workOrder->items as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_type' => $item->item_type ?? 'service',
                    'spare_part_id' => $item->spare_part_id,
                    'description' => $item->description ?: ($item->sparePart?->name ?? 'Jasa Servis'),
                    'qty' => $item->qty,
                    'unit_price_idr' => $item->unit_price_idr,
                    'subtotal_idr' => $item->subtotal_idr,
                ]);
            }

            // Buat Payment
            Payment::create([
                'paymentable_type' => SalesOrder::class,
                'paymentable_id' => $salesOrder->id,
                'method' => $paymentMethod,
                'status' => PaymentStatus::Success,
                'amount_idr' => $total,
                'reference' => 'WO-' . strtoupper(Str::random(10)),
                'paid_at' => now(),
            ]);

            // Buat Invoice
            Invoice::create([
                'invoiceable_type' => SalesOrder::class,
                'invoiceable_id' => $salesOrder->id,
                'number' => 'INV-WO-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'status' => 'paid',
                'due_at' => now(),
                'total_idr' => $total,
            ]);

            // Tambah poin loyalitas jika customer terdaftar
            if ($customer) {
                $points = (int) (($subtotal - $discount) / 10000);
                if ($points > 0) {
                    $customer->increment('loyalty_points', $points);
                    $customer->loyaltyTransactions()->create([
                        'type' => 'earning',
                        'points' => $points,
                        'reference_type' => SalesOrder::class,
                        'reference_id' => $salesOrder->id,
                        'description' => "Poin dari servis Work Order #{$workOrder->code}",
                    ]);
                }
            }

            // Update status Work Order langsung ke Delivered setelah dibayar lunas di kasir
            $oldStatus = $workOrder->status;
            $workOrder->update([
                'status' => WorkOrderStatus::Delivered,
                'completed_at' => $workOrder->completed_at ?? now(),
            ]);

            if ($workOrder->serviceBooking) {
                $workOrder->serviceBooking->update(['status' => 'completed']);
            }

            // Catat ke Audit Log
            AuditLog::create([
                'user_id'     => auth()->id() ?? $userId,
                'action'      => 'status_changed',
                'model_type'  => 'work_order',
                'model_id'    => $workOrder->id,
                'old_values'  => ['status' => $oldStatus->value ?? (string) $oldStatus],
                'new_values'  => ['status' => 'delivered'],
                'description' => "Work Order #{$workOrder->code} status changed from " . ($oldStatus->value ?? (string) $oldStatus) . " to delivered",
            ]);

            // Notifikasi ke Customer & Mekanik terkait
            $customerUser = $workOrder->vehicle?->customer?->user ?? $workOrder->serviceBooking?->customer?->user;
            $vehicleName = $workOrder->vehicle ? ($workOrder->vehicle->brand . ' ' . $workOrder->vehicle->model . ' (' . $workOrder->vehicle->plate_number . ')') : 'Kendaraan Pelanggan';

            try {
                $notifService = app(NotificationServiceContract::class);
                if ($customerUser) {
                    $notifService->send($customerUser, 'work_order_delivered', [
                        'title'           => "Pembayaran Diterima & Kendaraan Diserahkan: {$vehicleName}",
                        'message'         => "Pembayaran servis {$vehicleName} (WO #{$workOrder->code}) sebesar Rp " . number_format($total, 0, ',', '.') . " telah berhasil diproses di kasir. Kendaraan telah resmi diserahterimakan.",
                        'vehicle'         => $vehicleName,
                        'work_order_code' => $workOrder->code,
                        'action_url'      => route('workshop.tracking.show', $workOrder->id),
                    ]);
                }

                if ($workOrder->mechanic) {
                    $notifService->send($workOrder->mechanic, 'work_order_delivered', [
                        'title'           => "Unit WO #{$workOrder->code} Telah Lunas & Diserahkan",
                        'message'         => "Kendaraan {$vehicleName} (WO #{$workOrder->code}) telah lunas dibayar di Kasir dan resmi diserahterimakan kepada pelanggan.",
                        'vehicle'         => $vehicleName,
                        'work_order_code' => $workOrder->code,
                        'action_url'      => route('admin.work-orders.show', $workOrder->id),
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal mengirim notifikasi POS: " . $e->getMessage());
            }

            return $salesOrder;
        });
    }

    public function payCarSalesOrder(
        SalesOrder $salesOrder,
        int $discountIdr,
        string $paymentMethod,
        int $userId
    ): SalesOrder {
        return DB::transaction(function () use ($salesOrder, $discountIdr, $paymentMethod, $userId) {
            $salesOrder->load(['items', 'customer']);

            if ($salesOrder->status === SalesOrderStatus::Paid) {
                return $salesOrder;
            }

            $subtotal = $salesOrder->subtotal_idr;
            $discount = min($discountIdr, $subtotal);
            $total = max(0, $subtotal - $discount);

            $salesOrder->update([
                'status' => SalesOrderStatus::Paid,
                'discount_idr' => $discount,
                'total_idr' => $total,
                'paid_idr' => $total,
                'payment_status' => PaymentStatus::Success,
                'notes' => trim(($salesOrder->notes ?? '') . " | Lunas via " . strtoupper($paymentMethod)),
            ]);

            // Update status unit mobil ke Sold (Terjual)
            foreach ($salesOrder->items as $item) {
                if ($item->car_unit_id) {
                    CarUnit::where('id', $item->car_unit_id)->update(['status' => CarUnitStatus::Sold]);
                }
            }

            // Buat Payment record
            Payment::create([
                'paymentable_type' => SalesOrder::class,
                'paymentable_id' => $salesOrder->id,
                'method' => $paymentMethod,
                'status' => PaymentStatus::Success,
                'amount_idr' => $total,
                'reference' => 'CAR-' . strtoupper(Str::random(10)),
                'paid_at' => now(),
            ]);

            // Buat Invoice record
            Invoice::create([
                'invoiceable_type' => SalesOrder::class,
                'invoiceable_id' => $salesOrder->id,
                'number' => 'INV-CAR-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'status' => 'paid',
                'due_at' => now(),
                'total_idr' => $total,
            ]);

            // Tambah poin loyalitas untuk pembelian mobil jika ada customer
            if ($salesOrder->customer) {
                $points = (int) ($total / 50000);
                if ($points > 0) {
                    $salesOrder->customer->increment('loyalty_points', $points);
                    $salesOrder->customer->loyaltyTransactions()->create([
                        'type' => 'earning',
                        'points' => $points,
                        'reference_type' => SalesOrder::class,
                        'reference_id' => $salesOrder->id,
                        'description' => "Poin dari transaksi pembelian mobil #{$salesOrder->code}",
                    ]);
                }
            }

            return $salesOrder;
        });
    }

    protected function generateOrderCode(): string
    {
        do {
            $code = 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (SalesOrder::where('code', $code)->exists());


        return $code;
    }
}



