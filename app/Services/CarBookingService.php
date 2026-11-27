<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CarUnitStatus;
use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Beli mobil dengan booking fee: unit terkunci setelah booking fee diterima,
 * pelunasan dalam batas waktu, lalu diambil sendiri atau diantar towing.
 */
class CarBookingService
{
    public function __construct(protected NotificationService $notificationService) {}

    public function book(
        Customer $customer,
        CarUnit $car,
        string $paymentChannel,
        string $fulfillment,
        ?string $address = null
    ): SalesOrder {
        if ($car->status !== CarUnitStatus::Available || ! $car->isPubliclyListed()) {
            throw new \InvalidArgumentException('Unit tidak tersedia untuk dibooking.');
        }
        if ($car->isSoldBy($customer)) {
            throw new \InvalidArgumentException('Anda tidak bisa memesan mobil titipan Anda sendiri.');
        }

        $existing = SalesOrder::where('customer_id', $customer->id)
            ->where('status', SalesOrderStatus::Pending)
            ->whereHas('items', fn ($q) => $q->where('car_unit_id', $car->id))
            ->first();
        if ($existing) {
            return $existing;
        }

        $delivery = $fulfillment === 'delivery';
        $towing = $delivery ? PaymentSettings::towingFee() : 0;

        return DB::transaction(function () use ($customer, $car, $paymentChannel, $delivery, $towing, $address) {
            $total = (int) $car->price_sell_idr + $towing;

            $order = SalesOrder::create([
                'code' => 'SO-'.date('Ymd').'-'.strtoupper(Str::random(6)),
                'customer_id' => $customer->id,
                'type' => SalesOrderType::CarSales,
                'status' => SalesOrderStatus::Pending,
                'subtotal_idr' => $car->price_sell_idr,
                'discount_idr' => 0,
                'tax_idr' => 0,
                'shipping_fee_idr' => $towing,
                'total_idr' => $total,
                'deposit_idr' => min(PaymentSettings::bookingFee(), $total),
                'paid_idr' => 0,
                'payment_channel' => $paymentChannel,
                'payment_status' => $paymentChannel === 'online_transfer' ? PaymentStatus::WaitingUpload->value : PaymentStatus::Pending->value,
                'fulfillment_method' => $delivery ? 'delivery' : 'self_pickup',
                'shipping_address' => $delivery ? $address : null,
                'reserved_until' => now()->addHours(PaymentSettings::deadlineHours()),
                'notes' => "Booking unit {$car->full_name}",
                'created_by' => $customer->user_id,
            ]);

            SalesOrderItem::create([
                'sales_order_id' => $order->id,
                'item_type' => 'car',
                'car_unit_id' => $car->id,
                'description' => $car->full_name,
                'qty' => 1,
                'unit_price_idr' => $car->price_sell_idr,
                'subtotal_idr' => $car->price_sell_idr,
            ]);

            $this->notificationService->notifyRoles(['sales'], 'offer_submitted', [
                'title' => "Booking Unit Baru: {$car->full_name}",
                'message' => 'Customer membuat booking, menunggu booking fee.',
                'action_url' => route('admin.cars.show', $car),
            ]);

            return $order;
        });
    }

    /** Booking fee diterima: kunci unit. Gagal bila unit sudah diambil pembeli lain. */
    public function recordDeposit(SalesOrder $order): void
    {
        $carId = $order->items()->whereNotNull('car_unit_id')->value('car_unit_id');
        $car = CarUnit::whereKey($carId)->lockForUpdate()->first();

        if (! $car || $car->status !== CarUnitStatus::Available) {
            throw new \InvalidArgumentException('Unit sudah dipesan pembeli lain. Tolak pembayaran ini dan kembalikan dananya.');
        }

        $car->update(['status' => CarUnitStatus::Reserved]);
        $order->update([
            'payment_status' => 'dp_paid',
            'reserved_until' => now()->addDays(PaymentSettings::settlementDays()),
        ]);

        if ($user = $order->customer?->user) {
            $this->notificationService->send($user, 'payment_success', [
                'title' => "Booking Fee Diterima: #{$order->code}",
                'amount' => $order->deposit_idr,
                'message' => "Unit terkunci untuk Anda. Lunasi sebelum {$order->reserved_until->format('d M Y')}.",
                'action_url' => $order->customerUrl(),
            ]);
        }
    }

    /** Pesanan mobil yang kedaluwarsa: buka kembali unit yang terkunci. */
    public function releaseExpired(SalesOrder $order): void
    {
        if ($order->paid_idr <= 0) {
            return;
        }

        $carId = $order->items()->whereNotNull('car_unit_id')->value('car_unit_id');
        CarUnit::whereKey($carId)->where('status', CarUnitStatus::Reserved)->update(['status' => CarUnitStatus::Available]);

        $this->notificationService->notifyRoles(['manager', 'sales', 'super-admin'], 'booking_expired', [
            'title' => "Pelunasan Terlewat: #{$order->code}",
            'message' => 'Booking fee Rp '.number_format($order->paid_idr, 0, ',', '.').' perlu diputuskan hangus atau dikembalikan.',
            'action_url' => route('admin.approvals.index'),
        ]);
    }
}
