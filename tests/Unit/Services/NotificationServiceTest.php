<?php

namespace Tests\Unit\Services;

use App\Modules\Transfer\Contracts\NotificationStrategyInterface;
use App\Modules\Transfer\Factories\NotificationStrategyFactory;
use App\Modules\Transfer\Services\NotificationService;
use Mockery;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    private $factory;
    private $strategy;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = Mockery::mock(NotificationStrategyFactory::class);
        $this->strategy = Mockery::mock(NotificationStrategyInterface::class);
        $this->service = new NotificationService($this->factory);
    }

    public function test_send_notification_sends_email_and_sms_by_default()
    {
        $data = ['email' => 'test@example.com', 'phone_number' => '123456789'];
        $message = 'Message';

        // Expect Factory to create strategies
        $this->factory->shouldReceive('make')
            ->once()
            ->with(\App\Modules\Transfer\Enums\NotificationChannelEnum::EMAIL)
            ->andReturn($this->strategy);

        $this->factory->shouldReceive('make')
            ->once()
            ->with(\App\Modules\Transfer\Enums\NotificationChannelEnum::SMS)
            ->andReturn($this->strategy);

        // Expect Strategy to send
        $this->strategy->shouldReceive('send')
            ->twice()
            ->with($data, $message)
            ->andReturn(true);

        $this->assertTrue($this->service->sendNotification($data, $message));
    }

    public function test_send_notification_sends_only_email_when_specified()
    {
        $data = ['email' => 'test@example.com', 'phone_number' => '123456789'];
        $message = 'Message';

        $this->factory->shouldReceive('make')
            ->once()
            ->with(\App\Modules\Transfer\Enums\NotificationChannelEnum::EMAIL)
            ->andReturn($this->strategy);

        $this->factory->shouldNotReceive('make')
            ->with(\App\Modules\Transfer\Enums\NotificationChannelEnum::SMS);

        $this->strategy->shouldReceive('send')
            ->once()
            ->with($data, $message)
            ->andReturn(true);

        $this->assertTrue($this->service->sendNotification($data, $message, [\App\Modules\Transfer\Enums\NotificationChannelEnum::EMAIL]));
    }

    public function test_send_notification_returns_false_when_one_strategy_fails()
    {
        $data = ['email' => 'test@example.com', 'phone_number' => '123456789'];
        $message = 'Message';

        // Email succeeds
        $this->factory->shouldReceive('make')
            ->once()
            ->with(\App\Modules\Transfer\Enums\NotificationChannelEnum::EMAIL)
            ->andReturn($this->strategy);

        $this->strategy->shouldReceive('send')
            ->once() // First call succeeds
            ->andReturn(true);

        // SMS fails
        $this->factory->shouldReceive('make')
            ->once()
            ->with(\App\Modules\Transfer\Enums\NotificationChannelEnum::SMS)
            ->andReturn($this->strategy);

        $this->strategy->shouldReceive('send')
            ->once() // Second call fails
            ->andReturn(false);

        $this->assertFalse($this->service->sendNotification($data, $message));
    }
}
