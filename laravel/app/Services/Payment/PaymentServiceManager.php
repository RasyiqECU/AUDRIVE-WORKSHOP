<?php


declare(strict_types=1);


namespace App\Services\Payment;


use App\Services\Payment\Contracts\PaymentServiceContract;
use InvalidArgumentException;


class PaymentServiceManager
{
    protected array $drivers = [];


    public function __construct()
    {
        $this->drivers = [
            'fake' => FakePaymentService::class,
            'midtrans' => MidtransPaymentService::class,
            'xendit' => XenditPaymentService::class,
        ];
    }


    /**
     * Get payment service driver
     */
    public function driver(?string $driver = null): PaymentServiceContract
    {
        $driver = $driver ?? config('services.payment.driver', 'fake');


        if (!isset($this->drivers[$driver])) {
            throw new InvalidArgumentException("Payment driver [{$driver}] not supported.");
        }


        $driverClass = $this->drivers[$driver];


        return app($driverClass);
    }


    /**
     * Get default driver
     */
    public function getDefaultDriver(): string
    {
        return config('services.payment.driver', 'fake');
    }
}



