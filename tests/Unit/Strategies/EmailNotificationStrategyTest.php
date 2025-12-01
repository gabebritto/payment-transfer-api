<?php

namespace Tests\Unit\Strategies;

use App\Modules\Transfer\Clients\EmailNotificationClient;
use App\Modules\Transfer\Strategies\EmailNotificationStrategy;
use Mockery;
use Tests\TestCase;

class EmailNotificationStrategyTest extends TestCase
{
    public function test_send_calls_client_with_correct_arguments()
    {
        $client = Mockery::mock(EmailNotificationClient::class);
        $strategy = new EmailNotificationStrategy($client);

        $data = ['email' => 'test@example.com'];
        $message = 'Test Message';

        $client->shouldReceive('notify')
            ->once()
            ->with('test@example.com', 'Test Message')
            ->andReturn(true);

        $this->assertTrue($strategy->send($data, $message));
    }
}
