<?php

namespace Tests\Unit\Clients;

use App\Clients\NotificationClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NotificationClientTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new NotificationClient;
    }

    public function test_notify_returns_true_when_api_succeeds()
    {
        Http::fake([
            'util.devi.tools/api/v1/notify' => Http::response(null, 204),
        ]);

        $this->assertTrue($this->client->notify('test@example.com', '123456789', 'Message'));
    }

    public function test_notify_returns_false_when_api_fails()
    {
        Http::fake([
            'util.devi.tools/api/v1/notify' => Http::response(null, 500),
        ]);

        $this->assertFalse($this->client->notify('test@example.com', '123456789', 'Message'));
    }
}
