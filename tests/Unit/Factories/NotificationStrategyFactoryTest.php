<?php

namespace Tests\Unit\Factories;

use App\Modules\Transfer\Clients\EmailNotificationClient;
use App\Modules\Transfer\Clients\SmsNotificationClient;
use App\Modules\Transfer\Enums\NotificationChannelEnum;
use App\Modules\Transfer\Factories\NotificationStrategyFactory;
use App\Modules\Transfer\Strategies\EmailNotificationStrategy;
use App\Modules\Transfer\Strategies\SmsNotificationStrategy;
use Mockery;
use Tests\TestCase;

class NotificationStrategyFactoryTest extends TestCase
{
    private $emailClient;

    private $smsClient;

    private $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emailClient = Mockery::mock(EmailNotificationClient::class);
        $this->smsClient = Mockery::mock(SmsNotificationClient::class);
        $this->factory = new NotificationStrategyFactory($this->emailClient, $this->smsClient);
    }

    public function test_make_returns_email_strategy()
    {
        $strategy = $this->factory->make(NotificationChannelEnum::EMAIL);
        $this->assertInstanceOf(EmailNotificationStrategy::class, $strategy);
    }

    public function test_make_returns_sms_strategy()
    {
        $strategy = $this->factory->make(NotificationChannelEnum::SMS);
        $this->assertInstanceOf(SmsNotificationStrategy::class, $strategy);
    }
}
