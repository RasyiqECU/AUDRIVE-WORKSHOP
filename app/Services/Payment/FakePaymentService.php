<?php


declare(strict_types=1);


namespace App\Services\Payment;


use App\Models\SalesOrder;
use App\Services\Payment\Contracts\PaymentServiceContract;
use Illuminate\Support\Str;


class FakePaymentService implements PaymentServiceContract
{
    /**
     * Create payment intent (fake)
     */
    public function createPaymentIntent(SalesOrder $order, array $options = []): array
    {
        $transactionId = 'FAKE-' . Str::uuid()->toString();


        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'payment_url' => url("/payment/fake/{$transactionId}"),
            'amount' => $order->total_amount,
            'currency' => 'IDR',
            'metadata' => [
                'order_id' => $order->id,
                'order_code' => $order->code,
            ],
        ];
    }


    /**
     * Get payment status (fake - always success)
     */
    public function getPaymentStatus(string $transactionId): array
    {
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'status' => 'success',
            'amount' => 0,
            'paid_at' => now(),
        ];
    }


    /**
     * Process webhook notification (fake)
     */
    public function processWebhook(array $payload): bool
    {
        // Fake webhook always succeeds
        return true;
    }


    /**
     * Refund payment (fake)
     */
    public function refund(string $transactionId, int $amount): bool
    {
        // Fake refund always succeeds
        return true;
    }


    /**
     * Simulate payment success (for testing)
     */
    public function simulateSuccess(string $transactionId): bool
    {
        return true;
    }


    /**
     * Simulate payment failure (for testing)
     */
    public function simulateFailure(string $transactionId, string $reason = 'Payment failed'): bool
    {
        return false;
    }
}



