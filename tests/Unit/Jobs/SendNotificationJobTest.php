<?php

namespace Tests\Unit\Jobs;

use App\Modules\Transfer\Jobs\SendNotificationJob;
use App\Modules\Transfer\Services\NotificationService;
use Mockery;
use Tests\TestCase;

class SendNotificationJobTest extends TestCase
{
    private $notificationService;

    private $email = 'test@example.com';

    private $phoneNumber = '123456789';

    private $message = 'Message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = Mockery::mock(NotificationService::class);
    }

    public function test_handle_calls_notification_service_with_correct_arguments()
    {
        $this->notificationService->shouldReceive('sendNotification')
            ->once()
            ->with(['email' => 'test@example.com', 'phone_number' => '123456789'], 'Message', [])
            ->andReturn(true);

        $job = new SendNotificationJob($this->email, $this->phoneNumber, $this->message);
        $job->handle($this->notificationService);
    }

    public function test_handle_releases_job_when_notification_service_returns_false()
    {
        $this->notificationService->shouldReceive('sendNotification')
            ->once()
            ->andReturn(false);

        $job = Mockery::mock(SendNotificationJob::class, [$this->email, $this->phoneNumber, $this->message])->makePartial();
        $job->shouldReceive('release')
            ->once()
            ->with(10);

        $job->handle($this->notificationService);
    }

    public function test_handle_does_not_release_job_when_notification_service_returns_true()
    {
        $this->notificationService->shouldReceive('sendNotification')
            ->once()
            ->andReturn(true);

        $job = Mockery::mock(SendNotificationJob::class, [$this->email, $this->phoneNumber, $this->message])->makePartial();
        $job->shouldNotReceive('release');

        $job->handle($this->notificationService);
    }
}
