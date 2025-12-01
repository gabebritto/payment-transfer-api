<?php

namespace Tests\Unit\Clients;

use App\Modules\Transfer\Clients\AuthorizationClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthorizationClientTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new AuthorizationClient;
    }

    public function test_authorize_returns_true_when_api_returns_authorized()
    {
        Http::fake([
            'util.devi.tools/api/v2/authorize' => Http::response(['data' => ['authorization' => true]], 200),
        ]);

        $this->assertTrue($this->client->authorize());
    }

    public function test_authorize_returns_false_when_api_returns_unauthorized()
    {
        Http::fake([
            'util.devi.tools/api/v2/authorize' => Http::response(['data' => ['authorization' => false]], 200),
        ]);

        $this->assertFalse($this->client->authorize());
    }

    public function test_authorize_returns_false_when_api_fails()
    {
        Http::fake([
            'util.devi.tools/api/v2/authorize' => Http::response(null, 500),
        ]);

        $this->assertFalse($this->client->authorize());
    }
}
