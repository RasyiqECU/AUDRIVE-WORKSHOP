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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CheckoutService
{
    public function __construct(
        protected CartService $cartService
    ) {}


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



