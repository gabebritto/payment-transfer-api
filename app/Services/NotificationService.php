<?php

namespace App\Services;

class NotificationService
{
    public function __construct(
        protected \App\Clients\NotificationClient $client
    ) {}

    public function sendNotification(string $email, string $phoneNumber, string $message): bool
    {
        try {
            return $this->client->notify($email, $phoneNumber, $message);
        } catch (\Exception $e) {
            return false;
        }
    }
}
