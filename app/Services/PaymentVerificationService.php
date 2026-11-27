<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Pembayaran transfer yang diverifikasi manual oleh Cashier (tanpa payment gateway). */
class PaymentVerificationService
{
    public function __construct(
        protected CheckoutService $checkoutService,
        protected StockReservationService $reservations,
        protected NotificationService $notificationService
    ) {}

    /**
     * @param  array{sender_name: string, destination_bank: string, amount_idr: int}  $data
     */
    public function submitProof(SalesOrder $order, array $data, UploadedFile $proof): Payment
    {
        // Disk "local" bersifat private: tidak bisa dibuka lewat URL publik
        $path = $proof->store('payment-proofs', 'local');

        try {
            $payment = DB::transaction(function () use ($order, $data, $path) {
                $order = SalesOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();

                if (! $order->awaitsProof()) {
                    throw new \InvalidArgumentException('Pesanan ini tidak sedang menunggu bukti transfer.');
                }

                $payment = Payment::create([
                    'paymentable_type' => SalesOrder::class,
                    'paymentable_id' => $order->id,
                    'method' => 'transfer',
                    'status' => PaymentStatus::WaitingVerification,
                    'amount_idr' => $data['amount_idr'],
                    'reference' => 'TRF-'.strtoupper(Str::random(10)),
                    'proof_path' => $path,
                    'sender_name' => $data['sender_name'],
                    'destination_bank' => $data['destination_bank'],
                    'uploaded_at' => now(),
                ]);

                // Selama menunggu verifikasi, order tidak boleh kedaluwarsa
                $order->update(['payment_status' => PaymentStatus::WaitingVerification, 'reserved_until' => null]);

                return $payment;
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        $this->notificationService->notifyRoles(['cashier'], 'payment_verification', [
            'title' => "Bukti Transfer Menunggu Verifikasi: #{$order->code}",
            'message' => 'Periksa mutasi rekening lalu approve atau reject.',
            'action_url' => route('admin.payment-verifications.index'),
        ]);

        return $payment;
    }

    public function approve(Payment $payment, User $cashier): SalesOrder
    {
        return DB::transaction(function () use ($payment, $cashier) {
            [$payment, $order] = $this->lockWaiting($payment);

            $due = $order->amountDue();
            $isDeposit = $order->paid_idr <= 0 && $order->deposit_idr > 0 && $order->deposit_idr < $order->total_idr;

            if ($payment->amount_idr < $due) {
                throw new \InvalidArgumentException('Nominal transfer kurang dari tagihan. Gunakan Reject dengan alasan "Nominal kurang".');
            }

            $payment->update([
                'status' => PaymentStatus::Success,
                'paid_at' => now(),
                'verified_by' => $cashier->id,
                'verified_at' => now(),
                'purpose' => $isDeposit ? 'booking_fee' : ($order->deposit_idr > 0 ? 'settlement' : 'full'),
            ]);
            $order->increment('paid_idr', $due);

            if ($order->paid_idr >= $order->total_idr) {
                $this->checkoutService->markPaid($order, $cashier->id);
            } else {
                app(CarBookingService::class)->recordDeposit($order);
            }

            return $order;
        });
    }

    public function reject(Payment $payment, User $cashier, string $reason): void
    {
        DB::transaction(function () use ($payment, $cashier, $reason) {
            [$payment, $order] = $this->lockWaiting($payment);

            $payment->update([
                'status' => PaymentStatus::Rejected,
                'rejection_reason' => $reason,
                'verified_by' => $cashier->id,
                'verified_at' => now(),
            ]);

            // Beri kesempatan unggah ulang dengan batas waktu baru
            $order->update([
                'payment_status' => PaymentStatus::Rejected,
                // Hanya pesanan yang mengunci stok yang punya batas waktu
                'reserved_until' => $order->stock_reserved ? now()->addHours(PaymentSettings::deadlineHours()) : null,
            ]);

            $this->notifyCustomer($order, "Bukti Transfer Ditolak: #{$order->code}", "Alasan: {$reason}. Silakan unggah ulang bukti yang benar.");
        });
    }

    /** Order yang melewati batas waktu: tandai kedaluwarsa dan kembalikan stok. */
    public function expireOverdue(): int
    {
        $expired = 0;
        $overdue = SalesOrder::where('status', SalesOrderStatus::Pending)
            ->whereNotNull('reserved_until')
            ->where('reserved_until', '<', now())
            ->pluck('id');

        foreach ($overdue as $id) {
            DB::transaction(function () use ($id, &$expired) {
                $order = SalesOrder::whereKey($id)->lockForUpdate()->first();

                // Dicek ulang setelah dikunci: bisa saja baru dibayar atau bukti baru diunggah
                if (! $order || $order->status !== SalesOrderStatus::Pending || ! $order->reserved_until?->isPast()) {
                    return;
                }

                $this->reservations->release($order);
                if ($order->type === SalesOrderType::CarSales) {
                    app(CarBookingService::class)->releaseExpired($order);
                }
                $order->update(['status' => SalesOrderStatus::Expired, 'payment_status' => PaymentStatus::Expired]);
                $order->invoice()->update(['status' => 'cancelled']);
                $this->notifyCustomer($order, "Pesanan Kedaluwarsa: #{$order->code}", 'Batas waktu pembayaran terlewati sehingga pesanan dibatalkan.');
                $expired++;
            });
        }

        return $expired;
    }

    /** @return array{0: Payment, 1: SalesOrder} */
    protected function lockWaiting(Payment $payment): array
    {
        $order = SalesOrder::whereKey($payment->paymentable_id)->lockForUpdate()->firstOrFail();
        $payment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

        if ($payment->status !== PaymentStatus::WaitingVerification || $order->status !== SalesOrderStatus::Pending) {
            throw new \InvalidArgumentException('Pembayaran ini sudah diproses.');
        }

        return [$payment, $order];
    }

    protected function notifyCustomer(SalesOrder $order, string $title, string $message): void
    {
        if ($user = $order->customer?->user) {
            $this->notificationService->send($user, 'payment_verification', [
                'title' => $title,
                'message' => $message,
                'action_url' => $order->customerUrl(),
            ]);
        }
    }
}
