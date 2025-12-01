<?php

namespace App\Modules\Transfer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    public $backoff = 10;

    public string $email;

    public string $phoneNumber;

    public string $message;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, string $phoneNumber, string $message)
    {
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Modules\Transfer\Services\NotificationService $notificationService): void
    {
        if (!$notificationService->sendNotification($this->email, $this->phoneNumber, $this->message)) {
            $this->release(10);
        }
    }
}
