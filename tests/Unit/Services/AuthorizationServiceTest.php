<?php

namespace Tests\Unit\Services;

use App\Clients\AuthorizationClient;
use App\Services\AuthorizationService;
use Mockery;
use Tests\TestCase;

class AuthorizationServiceTest extends TestCase
{
    private $client;

    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(AuthorizationClient::class);
        $this->service = new AuthorizationService($this->client);
    }

    public function test_is_authorized_returns_true_when_client_returns_authorized()
    {
        $this->client->shouldReceive('authorize')
            ->once()
            ->andReturn(true);

        $this->assertTrue($this->service->isAuthorized());
    }

    public function test_is_authorized_returns_false_when_client_returns_unauthorized()
    {
        $this->client->shouldReceive('authorize')
            ->once()
            ->andReturn(false);

        $this->assertFalse($this->service->isAuthorized());
    }

    public function test_is_authorized_returns_false_when_client_throws_exception()
    {
        $this->client->shouldReceive('authorize')
            ->once()
            ->andThrow(new \Exception('API Error'));

        $this->assertFalse($this->service->isAuthorized());
    }
}
