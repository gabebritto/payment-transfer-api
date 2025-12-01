<?php

namespace App\Modules\Transfer\Services;

use App\Modules\Transfer\Enums\NotificationChannelEnum;
use App\Modules\Transfer\Factories\NotificationStrategyFactory;

class NotificationService
{
    public function __construct(
        protected NotificationStrategyFactory $factory
    ) {}

    /**
     * @param  array<NotificationChannelEnum>  $channels
     */
    public function sendNotification(array $data, string $message, array $channels = []): bool
    {
        if (empty($channels)) {
            $channels = [NotificationChannelEnum::EMAIL, NotificationChannelEnum::SMS];
        }

        $allSuccessful = true;

        foreach ($channels as $channel) {
            $strategy = $this->factory->make($channel);

            if (! $strategy->send($data, $message)) {
                $allSuccessful = false;
            }
        }

        return $allSuccessful;
    }
}
