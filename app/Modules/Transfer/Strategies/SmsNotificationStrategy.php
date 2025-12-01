<?php

namespace App\Modules\Transfer\Strategies;

use App\Modules\Transfer\Clients\SmsNotificationClient;
use App\Modules\Transfer\Contracts\NotificationStrategyInterface;

class SmsNotificationStrategy implements NotificationStrategyInterface
{
    public function __construct(
        protected SmsNotificationClient $client
    ) {}

    public function send(array $data, string $message): bool
    {
        return $this->client->notify($data['phone_number'], $message);
    }
}
