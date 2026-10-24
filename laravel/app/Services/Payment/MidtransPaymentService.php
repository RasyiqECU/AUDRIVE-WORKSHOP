<?php


declare(strict_types=1);


namespace App\Services\Payment;


use App\Models\SalesOrder;
use App\Services\Payment\Contracts\PaymentServiceContract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class MidtransPaymentService implements PaymentServiceContract
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $baseUrl;


    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key', '');
        $this->clientKey = config('services.midtrans.client_key', '');
        $this->isProduction = config('services.midtrans.is_production', false);
        $this->baseUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }


    /**
     * Create payment intent (Midtrans Snap)
     */
    public function createPaymentIntent(SalesOrder $order, array $options = []): array
    {
        try {
            $payload = [
                'transaction_details' => [
                    'order_id' => $order->code,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $order->customer?->user?->name ?? 'Guest',
                    'email' => $order->customer?->user?->email ?? 'guest@example.com',
                    'phone' => $order->customer?->user?->phone ?? '',
                ],
                'enabled_payments' => [
                    'credit_card',
                    'bank_transfer',
                    'echannel',
                    'gopay',
                    'shopeepay',
                ],
            ];


            // In development, return fake response
            if (app()->environment('local', 'testing')) {
                return $this->createFakeResponse($order);
            }


            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->post("{$this->baseUrl}/transactions", $payload);


            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $data['token'],
                    'payment_url' => $data['redirect_url'],
                    'amount' => $order->total_amount,
                    'currency' => 'IDR',
                    'metadata' => [
                        'order_id' => $order->id,
                        'order_code' => $order->code,
                    ],
                ];
            }


            throw new \Exception('Midtrans API error: ' . $response->body());


        } catch (\Exception $e) {
            Log::error('Midtrans createPaymentIntent failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);


            // Fallback to fake response in development
            if (app()->environment('local', 'testing')) {
                return $this->createFakeResponse($order);
            }


            throw $e;
        }
    }


    /**
     * Get payment status from Midtrans
     */
    public function getPaymentStatus(string $transactionId): array
    {
        try {
            // In development, return fake response
            if (app()->environment('local', 'testing')) {
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'status' => 'success',
                    'amount' => 0,
                    'paid_at' => now(),
                ];
            }


            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->get("https://api.midtrans.com/v2/{$transactionId}/status");


            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'status' => $this->mapMidtransStatus($data['transaction_status']),
                    'amount' => (int) ($data['gross_amount'] ?? 0),
                    'paid_at' => isset($data['settlement_time']) ? $data['settlement_time'] : null,
                ];
            }


            throw new \Exception('Midtrans API error: ' . $response->body());


        } catch (\Exception $e) {
            Log::error('Midtrans getPaymentStatus failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);


            throw $e;
        }
    }


    /**
     * Process webhook notification from Midtrans
     */
    public function processWebhook(array $payload): bool
    {
        try {
            // Verify signature
            $signatureKey = $payload['signature_key'] ?? '';
            $orderId = $payload['order_id'] ?? '';
            $statusCode = $payload['status_code'] ?? '';
            $grossAmount = $payload['gross_amount'] ?? '';


            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);


            if ($signatureKey !== $expectedSignature) {
                Log::warning('Midtrans webhook signature mismatch', [
                    'order_id' => $orderId,
                ]);
                return false;
            }


            // Process based on transaction status
            $transactionStatus = $payload['transaction_status'] ?? '';
            $fraudStatus = $payload['fraud_status'] ?? '';


            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'challenge') {
                    // TODO: Handle challenge status
                    Log::info('Midtrans payment challenge', ['order_id' => $orderId]);
                } elseif ($fraudStatus === 'accept') {
                    // TODO: Mark payment as success
                    Log::info('Midtrans payment success', ['order_id' => $orderId]);
                }
            } elseif ($transactionStatus === 'settlement') {
                // TODO: Mark payment as success
                Log::info('Midtrans payment settled', ['order_id' => $orderId]);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                // TODO: Mark payment as failed
                Log::info('Midtrans payment failed', [
                    'order_id' => $orderId,
                    'status' => $transactionStatus,
                ]);
            }


            return true;


        } catch (\Exception $e) {
            Log::error('Midtrans webhook processing failed', [
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }


    /**
     * Refund payment via Midtrans
     */
    public function refund(string $transactionId, int $amount): bool
    {
        try {
            // In development, return fake success
            if (app()->environment('local', 'testing')) {
                return true;
            }


            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->post("https://api.midtrans.com/v2/{$transactionId}/refund", [
                'amount' => $amount,
            ]);


            return $response->successful();


        } catch (\Exception $e) {
            Log::error('Midtrans refund failed', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }


    /**
     * Create fake response for development
     */
    protected function createFakeResponse(SalesOrder $order): array
    {
        $transactionId = 'MIDTRANS-FAKE-' . Str::uuid()->toString();


        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'payment_url' => url("/payment/midtrans/fake/{$transactionId}"),
            'amount' => $order->total_amount,
            'currency' => 'IDR',
            'metadata' => [
                'order_id' => $order->id,
                'order_code' => $order->code,
            ],
        ];
    }


    /**
     * Map Midtrans status to internal status
     */
    protected function mapMidtransStatus(string $midtransStatus): string
    {
        return match ($midtransStatus) {
            'capture', 'settlement' => 'success',
            'pending' => 'pending',
            'deny', 'cancel', 'expire', 'failure' => 'failed',
            'refund', 'partial_refund' => 'refunded',
            default => 'unknown',
        };
    }
}



