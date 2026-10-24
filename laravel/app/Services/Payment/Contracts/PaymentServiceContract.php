<?php


declare(strict_types=1);


namespace App\Services\Payment\Contracts;


use App\Models\SalesOrder;


interface PaymentServiceContract
{
    /**
     * Create payment intent
     */
    public function createPaymentIntent(SalesOrder $order, array $options = []): array;


    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId): array;


    /**
     * Process webhook notification
     */
    public function processWebhook(array $payload): bool;


    /**
     * Refund payment
     */
    public function refund(string $transactionId, int $amount): bool;
}



