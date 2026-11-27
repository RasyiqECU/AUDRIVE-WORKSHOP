<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\AuditLog;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Str;

/** Tagihan servis: terbit saat lolos QC, lunas lewat kasir atau transfer. */
class ServiceBillingService
{
    /** Buat (atau samakan) tagihan yang masih menunggu pembayaran. */
    public function issue(WorkOrder $workOrder, ?int $userId = null): SalesOrder
    {
        $workOrder->loadMissing(['vehicle.customer', 'serviceBooking.customer']);
        $customer = $workOrder->vehicle?->customer ?? $workOrder->serviceBooking?->customer;

        $order = SalesOrder::where('work_order_id', $workOrder->id)
            ->where('status', SalesOrderStatus::Pending)
            ->first();

        // Diskon yang sudah disetujui Manager dipertahankan
        $subtotal = (int) $workOrder->total_idr;
        $discount = min((int) ($order?->discount_idr ?? 0), $subtotal);
        $tax = (int) ($subtotal * 0.11);
        $amounts = [
            'subtotal_idr' => $subtotal,
            'discount_idr' => $discount,
            'tax_idr' => $tax,
            'total_idr' => max(0, $subtotal + $tax - $discount),
        ];

        if ($order) {
            $order->update($amounts);
        } else {
            $order = SalesOrder::create($amounts + [
                'code' => 'POS-'.date('Ymd').'-'.strtoupper(Str::random(6)),
                'customer_id' => $customer?->id,
                'type' => SalesOrderType::Service,
                'status' => SalesOrderStatus::Pending,
                'paid_idr' => 0,
                'payment_status' => PaymentStatus::Pending->value,
                'payment_channel' => 'cashier',
                'work_order_id' => $workOrder->id,
                'sales_id' => User::where('role', 'sales')->whereKey($workOrder->serviceBooking?->checked_in_by)->value('id'),
                'notes' => "Pembayaran Work Order #{$workOrder->code}",
                'created_by' => $userId ?? auth()->id() ?? $customer?->user_id ?? 1,
            ]);
        }

        $order->items()->delete();
        foreach ($workOrder->billableItems()->with('sparePart')->get() as $item) {
            SalesOrderItem::create([
                'sales_order_id' => $order->id,
                'item_type' => $item->type === 'part' ? 'spare_part' : 'service',
                'spare_part_id' => $item->spare_part_id,
                'description' => $item->description ?: ($item->sparePart?->name ?? 'Jasa Servis'),
                'qty' => $item->qty,
                'unit_price_idr' => $item->unit_price_idr,
                'subtotal_idr' => $item->subtotal_idr,
            ]);
        }

        return $order;
    }

    /** Dipanggil sekali saat tagihan servis lunas. */
    public function settled(SalesOrder $order, ?int $actorId = null): void
    {
        $workOrder = WorkOrder::find($order->work_order_id);
        if (! $workOrder) {
            return;
        }

        $workOrder->update(['paid_at' => now()]);

        $customer = $order->customer;
        $points = (int) (max(0, $order->subtotal_idr - $order->discount_idr) / 10000);
        if ($customer && $points > 0) {
            $customer->increment('loyalty_points', $points);
            $customer->loyaltyTransactions()->create([
                'type' => 'earning',
                'points' => $points,
                'reference_type' => SalesOrder::class,
                'reference_id' => $order->id,
                'description' => "Poin dari servis Work Order #{$workOrder->code}",
            ]);
        }

        AuditLog::create([
            'user_id' => $actorId ?? $order->created_by,
            'action' => 'work_order_paid',
            'model_type' => 'work_order',
            'model_id' => $workOrder->id,
            'old_values' => ['paid_at' => null],
            'new_values' => ['paid_at' => now()->toDateTimeString()],
            'description' => "Work Order #{$workOrder->code} lunas",
        ]);
    }
}
