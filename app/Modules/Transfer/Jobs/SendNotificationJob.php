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

    public array $channels;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, string $phoneNumber, string $message, array $channels = [])
    {
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->channels = $channels;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Modules\Transfer\Services\NotificationService $notificationService): void
    {
        $data = [
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
        ];

        if (! $notificationService->sendNotification($data, $this->message, $this->channels)) {
            $this->release(10);
        }
    }
}
