<?php


namespace App\Jobs;


use App\Models\Customer;
use App\Services\LoyaltyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class RecalculateLoyaltyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 60;


    public function __construct(
        protected int $customerId
    ) {}


    public function handle(LoyaltyService $loyaltyService): void
    {
        try {
            $customer = Customer::find($this->customerId);


            if (!$customer) {
                throw new \Exception("Customer not found: {$this->customerId}");
            }


            $loyaltyService->recalculatePoints($customer);


            Log::info("Loyalty points recalculated", [
                'customer_id' => $this->customerId,
                'new_points' => $customer->fresh()->loyalty_points,
            ]);


        } catch (\Exception $e) {
            Log::error("Loyalty recalculation failed", [
                'customer_id' => $this->customerId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}



