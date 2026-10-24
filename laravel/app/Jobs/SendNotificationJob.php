<?php


namespace App\Jobs;


use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 30;


    public function __construct(
        protected int $userId,
        protected string $type,
        protected array $data = []
    ) {}


    public function handle(NotificationService $notificationService): void
    {
        try {
            $user = User::find($this->userId);


            if (!$user) {
                throw new \Exception("User not found: {$this->userId}");
            }


            $success = $notificationService->send($user, $this->type, $this->data);


            if (!$success) {
                throw new \Exception("Notification send failed");
            }


            Log::info("Notification sent successfully", [
                'user_id' => $this->userId,
                'type' => $this->type,
            ]);


        } catch (\Exception $e) {
            Log::error("Notification send failed", [
                'user_id' => $this->userId,
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}



