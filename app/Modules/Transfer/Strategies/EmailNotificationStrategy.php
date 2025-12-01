<?php

namespace App\Modules\Transfer\Strategies;

use App\Modules\Transfer\Clients\EmailNotificationClient;
use App\Modules\Transfer\Contracts\NotificationStrategyInterface;

class EmailNotificationStrategy implements NotificationStrategyInterface
{
    public function __construct(
        protected EmailNotificationClient $client
    ) {}

    public function send(array $data, string $message): bool
    {
        return $this->client->notify($data['email'], $message);
    }
}
