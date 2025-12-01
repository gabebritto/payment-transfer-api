<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ObservabilityTest extends TestCase
{
    public function test_request_has_request_id_header()
    {
        $response = $this->get('/api/non-existent-route');

        $response->assertHeader('X-Request-ID');
    }

    public function test_request_id_is_added_to_context()
    {
        $this->get('/api/non-existent-route');

        $this->assertArrayHasKey('request_id', Context::all());
    }
}
