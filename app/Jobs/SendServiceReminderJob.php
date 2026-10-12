<?php


namespace App\Jobs;


use App\Models\CustomerVehicle;
use App\Services\Notification\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class SendServiceReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 30;


    public function __construct(
        protected int $vehicleId
    ) {}


    public function handle(NotificationService $notificationService): void
    {
        try {
            $vehicle = CustomerVehicle::with('customer.user')->find($this->vehicleId);


            if (!$vehicle || !$vehicle->customer) {
                throw new \Exception("Vehicle or customer not found: {$this->vehicleId}");
            }


            $user = $vehicle->customer->user;


            $data = [
                'vehicle' => $vehicle->brand . ' ' . $vehicle->model . ' ' . $vehicle->year,
                'customer_name' => $user->name,
            ];


            $success = $notificationService->send($user, 'service_reminder', $data);


            if (!$success) {
                throw new \Exception("Service reminder notification failed");
            }


            Log::info("Service reminder sent", [
                'vehicle_id' => $this->vehicleId,
                'user_id' => $user->id,
            ]);


        } catch (\Exception $e) {
            Log::error("Service reminder failed", [
                'vehicle_id' => $this->vehicleId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}



