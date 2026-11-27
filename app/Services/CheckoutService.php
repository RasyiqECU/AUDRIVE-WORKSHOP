<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CarUnitStatus;
use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\Cart;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected ?NotificationService $notificationService = null,
        protected ?StockReservationService $reservations = null
    ) {
        $this->notificationService = $notificationService ?? app(NotificationService::class);
        $this->reservations = $reservations ?? app(StockReservationService::class);
    }

    /**
     * Buat pesanan dan kunci stoknya. Stok fisik baru dipotong saat lunas.
     */
    public function processCheckout(
        Cart $cart,
        ?Customer $customer,
        string $shippingMethod,
        ?string $shippingAddress = null,
        string $paymentChannel = 'cashier'
    ): SalesOrder {
        $order = DB::transaction(function () use ($cart, $customer, $shippingMethod, $shippingAddress, $paymentChannel) {
            $items = $cart->items()->with('sparePart')->get();

            $this->reservations->reserve(
                $items->groupBy('spare_part_id')->map(fn ($rows) => (int) $rows->sum('qty'))->all()
            );

            $delivery = $shippingMethod === 'delivery';
            $subtotal = (int) $items->sum(fn ($item) => $item->qty * $item->price_idr);
            $tax = (int) ($subtotal * 0.11); // 11% tax
            $shippingFee = $delivery ? PaymentSettings::shippingFee() : 0;

            $salesOrder = SalesOrder::create([
                'code' => $this->generateOrderCode(),
                'customer_id' => $customer?->id,
                'type' => SalesOrderType::SparePart,
                'status' => SalesOrderStatus::Pending,
                'subtotal_idr' => $subtotal,
                'discount_idr' => 0,
                'tax_idr' => $tax,
                'shipping_fee_idr' => $shippingFee,
                'total_idr' => $subtotal + $tax + $shippingFee,
                'paid_idr' => 0,
                'payment_status' => $paymentChannel === 'online_transfer' ? PaymentStatus::WaitingUpload : PaymentStatus::Pending,
                'payment_channel' => $paymentChannel,
                'fulfillment_method' => $delivery ? 'delivery' : 'self_pickup',
                'shipping_address' => $delivery ? $shippingAddress : null,
                'stock_reserved' => true,
                'reserved_until' => now()->addHours(PaymentSettings::deadlineHours()),
                'notes' => "Shipping: {$shippingMethod}".($shippingAddress ? " | Address: {$shippingAddress}" : ''),
                'created_by' => $customer?->user_id ?? 1, // Default to admin if guest
            ]);

            foreach ($items as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_type' => 'spare_part',
                    'spare_part_id' => $item->spare_part_id,
                    'description' => $item->sparePart->name,
                    'qty' => $item->qty,
                    'unit_price_idr' => $item->price_idr,
                    'subtotal_idr' => $item->qty * $item->price_idr,
                ]);
            }

            $this->cartService->clearCart($cart);

            return $salesOrder;
        });

        $customerUser = $customer?->user;
        $total = 'Rp '.number_format($order->total_idr, 0, ',', '.');
        if ($customerUser) {
            $this->notificationService->send($customerUser, 'order_created', [
                'title' => "Pesanan Berhasil Dibuat: #{$order->code}",
                'message' => "Pesanan suku cadang #{$order->code} senilai {$total}. Bayar sebelum {$order->reserved_until->format('d M Y H:i')}.",
                'action_url' => route('parts.orders.show', $order->id),
            ]);
        }

        $this->notificationService->notifyRoles(['cashier', 'super-admin'], 'order_created', [
            'title' => "Pesanan Suku Cadang Masuk: #{$order->code}",
            'message' => 'Pesanan baru dari '.($customerUser?->name ?? 'Pelanggan')." senilai {$total}.",
            'action_url' => route('admin.pos.index'),
        ]);

        return $order;
    }

    /** Pembayaran di kasir (tunai/EDC). */
    public function processPayment(SalesOrder $salesOrder, string $method, int $amount): Payment
    {
        return DB::transaction(function () use ($salesOrder, $method, $amount) {
            $salesOrder = SalesOrder::whereKey($salesOrder->id)->lockForUpdate()->firstOrFail();

            if ($salesOrder->status !== SalesOrderStatus::Pending) {
                throw new \InvalidArgumentException('Pesanan ini sudah tidak menunggu pembayaran.');
            }

            $payment = Payment::create([
                'paymentable_type' => SalesOrder::class,
                'paymentable_id' => $salesOrder->id,
                'method' => $method,
                'status' => PaymentStatus::Success,
                'amount_idr' => $amount,
                'reference' => 'PAY-'.strtoupper(Str::random(10)),
                'paid_at' => now(),
            ]);

            $salesOrder->increment('paid_idr', $amount);

            if ($salesOrder->paid_idr >= $salesOrder->total_idr) {
                $this->markPaid($salesOrder, auth()->id());
            }

            return $payment;
        });
    }

    /**
     * Pesanan lunas (lewat kasir atau verifikasi transfer): potong stok,
     * terbitkan invoice, beri poin, beri tahu gudang.
     */
    public function markPaid(SalesOrder $order, ?int $actorId = null): void
    {
        // Pesanan lama (sebelum ada kuncian stok) sudah dipotong dan diberi poin saat checkout
        $firstSettlement = $this->reservations->consume($order, $actorId);

        $order->update([
            'status' => SalesOrderStatus::Paid,
            'payment_status' => PaymentStatus::Success,
            'reserved_until' => null,
        ]);

        // Invoice draft bisa sudah ada (customer membukanya sebelum bayar)
        app(InvoiceService::class)->ensureInvoiceForSalesOrder($order)
            ->update(['status' => 'paid', 'total_idr' => $order->total_idr]);

        if ($order->type === SalesOrderType::Service) {
            app(ServiceBillingService::class)->settled($order, $actorId);
        }

        $customer = $order->customer;
        if ($firstSettlement && $customer) {
            $points = (int) ($order->subtotal_idr / 10000); // 1 point per Rp 10.000
            if ($points > 0) {
                $customer->increment('loyalty_points', $points);
                $customer->loyaltyTransactions()->create([
                    'type' => 'earning',
                    'points' => $points,
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $order->id,
                    'description' => "Poin dari pembelian spare part #{$order->code}",
                ]);
            }
        }

        if ($order->type === SalesOrderType::CarSales) {
            $carIds = $order->items()->whereNotNull('car_unit_id')->pluck('car_unit_id');
            CarUnit::whereIn('id', $carIds)->update(['status' => CarUnitStatus::Sold]);

            $points = (int) ($order->total_idr / 50000);
            if ($customer && $points > 0) {
                $customer->increment('loyalty_points', $points);
                $customer->loyaltyTransactions()->create([
                    'type' => 'earning',
                    'points' => $points,
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $order->id,
                    'description' => "Poin dari transaksi pembelian mobil #{$order->code}",
                ]);
            }
        }

        $amount = 'Rp '.number_format($order->total_idr, 0, ',', '.');
        if ($customer?->user) {
            $this->notificationService->send($customer->user, 'payment_success', [
                'title' => "Pembayaran Berhasil: #{$order->code}",
                'message' => "Pembayaran {$amount} untuk pesanan #{$order->code} telah diverifikasi.",
                'amount' => $order->total_idr,
                'action_url' => $order->customerUrl(),
            ]);
        }

        $this->notificationService->notifyRoles(['cashier', 'sales', 'super-admin'], 'payment_success', [
            'title' => "Pembayaran Diterima: #{$order->code}",
            'message' => "Pembayaran {$amount} untuk pesanan #{$order->code} telah diterima.",
            'amount' => $order->total_idr,
            'action_url' => route('admin.pos.index'),
        ]);

        if ($order->type === SalesOrderType::SparePart) {
            $this->notificationService->notifyRoles(['inventory'], 'order_fulfilment', [
                'title' => "Siapkan Barang: #{$order->code}",
                'message' => $order->fulfillment_method === 'delivery' ? 'Pesanan lunas, siapkan untuk dikirim.' : 'Pesanan lunas, siapkan untuk diambil.',
                'action_url' => route('admin.fulfilments.index'),
            ]);
        }
    }

    protected function generateOrderCode(): string
    {
        do {
            $code = 'SO-'.date('Ymd').'-'.strtoupper(Str::random(6));
        } while (SalesOrder::where('code', $code)->exists());

        return $code;
    }
}
