<?php


declare(strict_types=1);


namespace App\Services;


use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Enums\StockMovementType;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SparePart;
use App\Models\StockMovement;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected ?NotificationService $notificationService = null
    ) {
        $this->notificationService = $notificationService ?? app(NotificationService::class);
    }


    public function processCheckout(
        Cart $cart,
        ?Customer $customer,
        string $shippingMethod,
        ?string $shippingAddress = null
    ): SalesOrder {
        return DB::transaction(function () use ($cart, $customer, $shippingMethod, $shippingAddress) {
            // Validate stock for all items
            foreach ($cart->items as $item) {
                $part = SparePart::where('id', $item->spare_part_id)
                    ->lockForUpdate()
                    ->first();


                if ($part->stock < $item->qty) {
                    throw new \Exception("Stok tidak cukup untuk {$part->name}. Tersedia: {$part->stock}");
                }
            }


            // Calculate totals
            $subtotal = $this->cartService->calculateTotal($cart);
            $tax = (int) ($subtotal * 0.11); // 11% tax
            $total = $subtotal + $tax;


            // Create sales order
            $salesOrder = SalesOrder::create([
                'code' => $this->generateOrderCode(),
                'customer_id' => $customer?->id,
                'type' => SalesOrderType::SparePart,
                'status' => SalesOrderStatus::Pending,
                'subtotal_idr' => $subtotal,
                'discount_idr' => 0,
                'tax_idr' => $tax,
                'total_idr' => $total,
                'paid_idr' => 0,
                'payment_status' => PaymentStatus::Pending,
                'notes' => "Shipping: {$shippingMethod}" . ($shippingAddress ? " | Address: {$shippingAddress}" : ''),
                'created_by' => $customer?->user_id ?? 1, // Default to admin if guest
            ]);


            // Create sales order items and reduce stock
            foreach ($cart->items as $item) {
                $part = SparePart::where('id', $item->spare_part_id)
                    ->lockForUpdate()
                    ->first();


                // Create sales order item
                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_type' => 'spare_part',
                    'spare_part_id' => $part->id,
                    'description' => $part->name,
                    'qty' => $item->qty,
                    'unit_price_idr' => $item->price_idr,
                    'subtotal_idr' => $item->qty * $item->price_idr,
                ]);


                // Reduce stock
                $part->decrement('stock', $item->qty);
                $part->refresh();

                // Peringatan stok menipis otomatis ke Inventory & Super Admin
                if ($part->stock <= ($part->low_stock_threshold ?? 5)) {
                    try {
                        $this->notificationService->notifyRoles(['inventory', 'super-admin'], 'low_stock_alert', [
                            'title'           => "Peringatan Stok Suku Cadang Menipis: {$part->name}",
                            'message'         => "Suku cadang {$part->name} tersisa {$part->stock} unit (Batas minimum: {$part->low_stock_threshold}). Segera lakukan restock.",
                            'part_id'         => $part->id,
                            'part_name'       => $part->name,
                            'remaining_stock' => $part->stock,
                            'action_url'      => route('admin.spare-parts.show', $part->id),
                        ]);
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi low stock: ' . $e->getMessage());
                    }
                }

                // Create stock movement
                StockMovement::create([
                    'spare_part_id' => $part->id,
                    'type' => StockMovementType::Out,
                    'qty' => $item->qty,
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $salesOrder->id,
                    'notes' => "Penjualan spare part - Order #{$salesOrder->code}",
                    'created_by' => $customer?->user_id ?? 1,
                ]);
            }

            // Add loyalty points if customer is logged in
            if ($customer) {
                $points = (int) ($subtotal / 10000); // 1 point per Rp 10.000
                if ($points > 0) {
                    $customer->increment('loyalty_points', $points);

                    // Create loyalty transaction
                    $customer->loyaltyTransactions()->create([
                        'type' => 'earning',
                        'points' => $points,
                        'reference_type' => SalesOrder::class,
                        'reference_id' => $salesOrder->id,
                        'description' => "Poin dari pembelian spare part #{$salesOrder->code}",
                    ]);
                }
            }

            // Clear cart
            $this->cartService->clearCart($cart);

            // Notifikasi Pembelian Baru ke Customer & Role Terkait
            $customerUser = $customer?->user;
            if ($customerUser) {
                $this->notificationService->send($customerUser, 'order_created', [
                    'title'      => "Pesanan Berhasil Dibuat: #{$salesOrder->code}",
                    'message'    => "Pesanan suku cadang #{$salesOrder->code} berhasil dibuat senilai Rp " . number_format($salesOrder->total_idr, 0, ',', '.') . ". Silakan lakukan pembayaran.",
                    'order_code' => $salesOrder->code,
                    'amount'     => $salesOrder->total_idr,
                    'action_url' => route('parts.orders.show', $salesOrder->id),
                ]);
            }

            // Notifikasi ke Tim Kasir & Gudang/Inventory + Super Admin
            $customerName = $customerUser?->name ?? 'Pelanggan';
            $this->notificationService->notifyRoles(['cashier', 'inventory', 'super-admin'], 'order_created', [
                'title'      => "Pesanan Suku Cadang Masuk: #{$salesOrder->code}",
                'message'    => "Pesanan suku cadang baru masuk dari {$customerName} (#{$salesOrder->code}) senilai Rp " . number_format($salesOrder->total_idr, 0, ',', '.'),
                'order_code' => $salesOrder->code,
                'amount'     => $salesOrder->total_idr,
                'action_url' => route('admin.pos.index'),
            ]);

            return $salesOrder;
        });
    }


    public function processPayment(SalesOrder $salesOrder, string $method, int $amount): Payment
    {
        return DB::transaction(function () use ($salesOrder, $method, $amount) {
            // Create payment
            $payment = Payment::create([
                'paymentable_type' => SalesOrder::class,
                'paymentable_id' => $salesOrder->id,
                'method' => $method,
                'status' => PaymentStatus::Success,
                'amount_idr' => $amount,
                'reference' => 'PAY-' . strtoupper(Str::random(10)),
                'paid_at' => now(),
            ]);


            // Update sales order
            $salesOrder->increment('paid_idr', $amount);


            if ($salesOrder->paid_idr >= $salesOrder->total_idr) {
                $salesOrder->update([
                    'payment_status' => PaymentStatus::Success,
                    'status' => SalesOrderStatus::Paid,
                ]);


                // Create invoice
                Invoice::create([
                    'invoiceable_type' => SalesOrder::class,
                    'invoiceable_id' => $salesOrder->id,
                    'number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                    'status' => 'paid',
                    'due_at' => now(),
                    'total_idr' => $salesOrder->total_idr,
                ]);
            }

            // Notifikasi Pembayaran Berhasil ke Customer
            $customerUser = $salesOrder->customer?->user;
            if ($customerUser) {
                $this->notificationService->send($customerUser, 'payment_success', [
                    'title'      => "Pembayaran Berhasil: #{$salesOrder->code}",
                    'message'    => "Pembayaran sebesar Rp " . number_format($amount, 0, ',', '.') . " untuk pesanan #{$salesOrder->code} telah diverifikasi.",
                    'order_code' => $salesOrder->code,
                    'amount'     => $amount,
                    'action_url' => route('parts.orders.show', $salesOrder->id),
                ]);
            }

            // Notifikasi ke Tim Kasir & Sales + Super Admin
            $this->notificationService->notifyRoles(['cashier', 'sales', 'super-admin'], 'payment_success', [
                'title'      => "Pembayaran Diterima: #{$salesOrder->code}",
                'message'    => "Pembayaran sebesar Rp " . number_format($amount, 0, ',', '.') . " untuk pesanan #{$salesOrder->code} telah diterima via {$method}.",
                'order_code' => $salesOrder->code,
                'amount'     => $amount,
                'action_url' => route('admin.pos.index'),
            ]);

            return $payment;
        });
    }


    protected function generateOrderCode(): string
    {
        do {
            $code = 'SO-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (SalesOrder::where('code', $code)->exists());


        return $code;
    }
}



