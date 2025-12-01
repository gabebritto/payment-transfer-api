<?php

namespace Tests\Unit\Clients;

use App\Modules\Transfer\Clients\EmailNotificationClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailNotificationClientTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new EmailNotificationClient;
    }

    public function test_notify_returns_true_when_api_succeeds()
    {
        Http::fake([
            'util.devi.tools/api/v1/notify' => Http::response(null, 204),
        ]);

        $this->assertTrue($this->client->notify('test@example.com', 'Message'));
    }

    public function test_notify_returns_false_when_api_fails()
    {
        Http::fake([
            'util.devi.tools/api/v1/notify' => Http::response(null, 500),
        ]);

        $this->assertFalse($this->client->notify('test@example.com', 'Message'));
    }
}
