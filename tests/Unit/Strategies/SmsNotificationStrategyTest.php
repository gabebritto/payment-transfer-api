<?php

namespace Tests\Unit\Strategies;

use App\Modules\Transfer\Clients\SmsNotificationClient;
use App\Modules\Transfer\Strategies\SmsNotificationStrategy;
use Mockery;
use Tests\TestCase;

class SmsNotificationStrategyTest extends TestCase
{
    public function test_send_calls_client_with_correct_arguments()
    {
        $client = Mockery::mock(SmsNotificationClient::class);
        $strategy = new SmsNotificationStrategy($client);

        $data = ['phone_number' => '123456789'];
        $message = 'Test Message';

        $client->shouldReceive('notify')
            ->once()
            ->with('123456789', 'Test Message')
            ->andReturn(true);

        $this->assertTrue($strategy->send($data, $message));
    }
}
