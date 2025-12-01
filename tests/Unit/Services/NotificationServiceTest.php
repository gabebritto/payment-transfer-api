<?php

namespace Tests\Unit\Services;

use App\Clients\NotificationClient;
use App\Services\NotificationService;
use Mockery;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    private $client;

    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(NotificationClient::class);
        $this->service = new NotificationService($this->client);
    }

    public function test_send_notification_returns_true_when_client_succeeds()
    {
        $this->client->shouldReceive('notify')
            ->once()
            ->with('test@example.com', '123456789', 'Message')
            ->andReturn(true);

        $this->assertTrue($this->service->sendNotification('test@example.com', '123456789', 'Message'));
    }

    public function test_send_notification_returns_false_when_client_fails()
    {
        $this->client->shouldReceive('notify')
            ->once()
            ->with('test@example.com', '123456789', 'Message')
            ->andReturn(false);

        $this->assertFalse($this->service->sendNotification('test@example.com', '123456789', 'Message'));
    }

    public function test_send_notification_returns_false_when_client_throws_exception()
    {
        $this->client->shouldReceive('notify')
            ->once()
            ->with('test@example.com', '123456789', 'Message')
            ->andThrow(new \Exception('API Error'));

        $this->assertFalse($this->service->sendNotification('test@example.com', '123456789', 'Message'));
    }
}
