<?php

namespace Tests\Feature;

use App\Modules\Transfer\Jobs\SendNotificationJob;
use App\Modules\User\Models\Retailer;
use App\Modules\User\Models\User;
use App\Modules\Wallet\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->mock(\App\Modules\Transfer\Services\AuthorizationService::class, function ($mock) {
            $mock->shouldReceive('isAuthorized')->andReturn(true);
        });
    }

    public function test_user_can_transfer_money_to_retailer()
    {
        $payer = User::factory()->create();
        $payerWallet = $payer->wallet()->create(['balance' => 100]);

        $payee = Retailer::factory()->create();
        $payeeWallet = $payee->wallet()->create(['balance' => 0]);

        $response = $this->postJson('/api/transfer', [
            'value' => 50,
            'payer' => $payerWallet->id,
            'payee' => $payeeWallet->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('wallets', [
            'owner_id' => $payer->id,
            'owner_type' => User::class,
            'balance' => 50,
        ]);

        $this->assertDatabaseHas('wallets', [
            'owner_id' => $payee->id,
            'owner_type' => Retailer::class,
            'balance' => 50,
        ]);

        $this->assertDatabaseHas('transactions', [
            'payer_id' => $payer->id,
            'payer_type' => User::class,
            'payee_id' => $payee->id,
            'payee_type' => Retailer::class,
            'value' => 50,
            'status' => 'completed',
        ]);

        Queue::assertPushed(SendNotificationJob::class, function ($job) use ($payee) {
            return $job->email === $payee->email &&
                $job->phoneNumber === $payee->phone_number &&
                $job->message === 'You received a payment of 50';
        });
    }

    public function test_retailer_cannot_transfer_money()
    {
        $payer = Retailer::factory()->create();
        $payerWallet = $payer->wallet()->create(['balance' => 100]);

        $payee = User::factory()->create();
        $payeeWallet = $payee->wallet()->create(['balance' => 0]);

        $response = $this->postJson('/api/transfer', [
            'value' => 50,
            'payer' => $payerWallet->id,
            'payee' => $payeeWallet->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Payer cannot send money.']);
    }

    public function test_user_cannot_transfer_with_insufficient_balance()
    {
        $payer = User::factory()->create();
        $payerWallet = $payer->wallet()->create(['balance' => 10]);

        $payee = Retailer::factory()->create();
        $payeeWallet = $payee->wallet()->create(['balance' => 0]);

        $response = $this->postJson('/api/transfer', [
            'value' => 50,
            'payer' => $payerWallet->id,
            'payee' => $payeeWallet->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Insufficient balance.']);
    }

    public function test_transfer_fails_if_authorization_service_denies()
    {
        // Override the default mock
        $this->mock(\App\Modules\Transfer\Services\AuthorizationService::class, function ($mock) {
            $mock->shouldReceive('isAuthorized')->andReturn(false);
        });

        $payer = User::factory()->create();
        $payerWallet = $payer->wallet()->create(['balance' => 100]);

        $payee = User::factory()->create();
        $payeeWallet = $payee->wallet()->create(['balance' => 0]);

        $response = $this->postJson('/api/transfer', [
            'value' => 50,
            'payer' => $payerWallet->id,
            'payee' => $payeeWallet->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Transfer not authorized by external service.']);
    }

    public function test_notification_job_is_dispatched()
    {
        $payer = User::factory()->has(Wallet::factory()->state(['balance' => 10000]))->create();
        $payee = User::factory()->has(Wallet::factory()->state(['balance' => 0]))->create();

        $transferService = app(\App\Modules\Transfer\Services\TransferService::class);
        $transferService->execute($payer->wallet->id, $payee->wallet->id, 1000);

        Queue::assertPushed(SendNotificationJob::class, function (SendNotificationJob $job) {
            return true;
        });
    }
}
