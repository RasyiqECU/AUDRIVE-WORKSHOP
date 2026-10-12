<?php


declare(strict_types=1);


namespace App\Services\Payment;


use App\Models\SalesOrder;
use App\Services\Payment\Contracts\PaymentServiceContract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class XenditPaymentService implements PaymentServiceContract
{
    protected string $secretKey;
    protected string $baseUrl;


    public function __construct()
    {
        $this->secretKey = config('services.xendit.secret_key', '');
        $this->baseUrl = config('services.xendit.base_url', 'https://api.xendit.co');
    }


    /**
     * Create payment intent (Xendit Payment Request)
     */
    public function createPaymentIntent(SalesOrder $order, array $options = []): array
    {
        try {
            $payload = [
                'amount' => (int) $order->total_amount,
                'currency' => 'IDR',
                'payment_method' => [
                    'type' => 'PAYLINK',
                    'reusability' => 'ONE_TIME_USE',
                ],
                'reference_id' => $order->code,
                'customer_id' => $order->customer?->id ? (string) $order->customer->id : null,
                'description' => "Payment for order {$order->code}",
                'metadata' => [
                    'order_id' => $order->id,
                    'order_code' => $order->code,
                ],
            ];


            // In development, return fake response
            if (app()->environment('local', 'testing')) {
                return $this->createFakeResponse($order);
            }


            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->withBasicAuth($this->secretKey, '')
              ->post("{$this->baseUrl}/payment_requests", $payload);


            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $data['id'],
                    'payment_url' => $data['payment_method']['actions']['desktop_web_checkout_url'] ?? null,
                    'amount' => $order->total_amount,
                    'currency' => 'IDR',
                    'metadata' => [
                        'order_id' => $order->id,
                        'order_code' => $order->code,
                    ],
                ];
            }


            throw new \Exception('Xendit API error: ' . $response->body());


        } catch (\Exception $e) {
            Log::error('Xendit createPaymentIntent failed', [
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
     * Get payment status from Xendit
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
                'Content-Type' => 'application/json',
            ])->withBasicAuth($this->secretKey, '')
              ->get("{$this->baseUrl}/payment_requests/{$transactionId}");


            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'status' => $this->mapXenditStatus($data['status']),
                    'amount' => (int) ($data['amount'] ?? 0),
                    'paid_at' => $data['created'] ?? null,
                ];
            }


            throw new \Exception('Xendit API error: ' . $response->body());


        } catch (\Exception $e) {
            Log::error('Xendit getPaymentStatus failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);


            throw $e;
        }
    }


    /**
     * Process webhook notification from Xendit
     */
    public function processWebhook(array $payload): bool
    {
        try {
            // Verify callback token
            $callbackToken = request()->header('x-callback-token') ?? '';
            $expectedToken = config('services.xendit.webhook_token', '');


            if ($callbackToken !== $expectedToken) {
                Log::warning('Xendit webhook token mismatch');
                return false;
            }


            // Process based on event type
            $eventType = $payload['event'] ?? '';
            $data = $payload['data'] ?? [];


            if ($eventType === 'payment_request.succeeded') {
                // TODO: Mark payment as success
                Log::info('Xendit payment success', [
                    'payment_request_id' => $data['id'] ?? '',
                ]);
            } elseif ($eventType === 'payment_request.failed') {
                // TODO: Mark payment as failed
                Log::info('Xendit payment failed', [
                    'payment_request_id' => $data['id'] ?? '',
                ]);
            }


            return true;


        } catch (\Exception $e) {
            Log::error('Xendit webhook processing failed', [
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }


    /**
     * Refund payment via Xendit
     */
    public function refund(string $transactionId, int $amount): bool
    {
        try {
            // In development, return fake success
            if (app()->environment('local', 'testing')) {
                return true;
            }


            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->withBasicAuth($this->secretKey, '')
              ->post("{$this->baseUrl}/refunds", [
                  'payment_request_id' => $transactionId,
                  'amount' => $amount,
                  'currency' => 'IDR',
              ]);


            return $response->successful();


        } catch (\Exception $e) {
            Log::error('Xendit refund failed', [
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
        $transactionId = 'XENDIT-FAKE-' . Str::uuid()->toString();


        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'payment_url' => url("/payment/xendit/fake/{$transactionId}"),
            'amount' => $order->total_amount,
            'currency' => 'IDR',
            'metadata' => [
                'order_id' => $order->id,
                'order_code' => $order->code,
            ],
        ];
    }


    /**
     * Map Xendit status to internal status
     */
    protected function mapXenditStatus(string $xenditStatus): string
    {
        return match ($xenditStatus) {
            'SUCCEEDED' => 'success',
            'PENDING' => 'pending',
            'FAILED', 'EXPIRED' => 'failed',
            'REFUNDED', 'PARTIALLY_REFUNDED' => 'refunded',
            default => 'unknown',
        };
    }
}



