<?php

namespace App\Modules\Transfer\Factories;

use App\Modules\Transfer\Clients\EmailNotificationClient;
use App\Modules\Transfer\Clients\SmsNotificationClient;
use App\Modules\Transfer\Contracts\NotificationStrategyInterface;
use App\Modules\Transfer\Enums\NotificationChannelEnum;
use App\Modules\Transfer\Strategies\EmailNotificationStrategy;
use App\Modules\Transfer\Strategies\SmsNotificationStrategy;

class NotificationStrategyFactory
{
    public function __construct(
        protected EmailNotificationClient $emailClient,
        protected SmsNotificationClient $smsClient
    ) {}

    public function make(NotificationChannelEnum $channel): NotificationStrategyInterface
    {
        return match ($channel) {
            NotificationChannelEnum::EMAIL => new EmailNotificationStrategy($this->emailClient),
            NotificationChannelEnum::SMS => new SmsNotificationStrategy($this->smsClient),
        };
    }
}
