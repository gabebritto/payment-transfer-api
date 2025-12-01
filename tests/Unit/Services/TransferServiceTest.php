<?php

namespace Tests\Unit\Services;

use App\Enums\TransactionEnum;
use App\Exceptions\TransactionException;
use App\Jobs\SendNotificationJob;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\Services\AuthorizationService;
use App\Services\TransferService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    private MockInterface $authService;

    private MockInterface $walletRepo;

    private MockInterface $transactionRepo;

    private TransferService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthorizationService::class);
        $this->walletRepo = Mockery::mock(WalletRepositoryInterface::class);
        $this->transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $this->service = new TransferService(
            $this->authService,
            $this->walletRepo,
            $this->transactionRepo
        );

        Queue::fake();
        DB::shouldReceive('transaction')->andReturnUsing(fn($callback) => $callback());
    }

    public function test_execute_throws_exception_when_payer_cannot_send_money()
    {
        $this->authService->shouldReceive('isAuthorized')->once()->andReturn(true);

        $payer = $this->mockUser(canSend: false);
        $payerWallet = $this->mockWallet('payer-id', $payer);
        $payeeWallet = $this->mockWallet('payee-id');

        $this->walletRepo->shouldReceive('lockForUpdate')->with('payer-id')->andReturn($payerWallet);
        $this->walletRepo->shouldReceive('lockForUpdate')->with('payee-id')->andReturn($payeeWallet);

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Payer cannot send money.');

        $this->service->execute('payer-id', 'payee-id', 50);
    }

    public function test_execute_throws_exception_when_payer_and_payee_are_same()
    {
        $this->authService->shouldReceive('isAuthorized')->never();
        $this->walletRepo->shouldReceive('findById')->never();

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Cannot transfer to same wallet.');

        $this->service->execute('same-id', 'same-id', 50);
    }

    public function test_execute_throws_exception_when_authorization_fails()
    {
        $this->authService->shouldReceive('isAuthorized')->once()->andReturn(false);

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Transfer not authorized by external service.');

        $this->service->execute('payer-id', 'payee-id', 50);
    }

    public function test_execute_throws_exception_when_balance_is_insufficient()
    {
        $this->authService->shouldReceive('isAuthorized')->once()->andReturn(true);

        $payer = $this->mockUser(canSend: true);
        $payerWallet = $this->mockWallet('payer-id', $payer, 10);
        $payeeWallet = $this->mockWallet('payee-id');

        $this->walletRepo->shouldReceive('lockForUpdate')->with('payer-id')->andReturn($payerWallet);
        $this->walletRepo->shouldReceive('lockForUpdate')->with('payee-id')->andReturn($payeeWallet);

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Insufficient balance.');

        $this->service->execute('payer-id', 'payee-id', 50);
    }

    public function test_execute_completes_successfully()
    {
        $this->authService->shouldReceive('isAuthorized')->once()->andReturn(true);

        $payer = $this->mockUser(true);
        $payee = $this->mockUser(true);

        $senderLocked = $this->mockWallet('payer-id', $payer, 100);
        $senderLocked->shouldReceive('decrement')->with('balance', 50)->once();

        $receiverLocked = $this->mockWallet('payee-id', $payee);
        $receiverLocked->shouldReceive('increment')->with('balance', 50)->once();

        $this->walletRepo->shouldReceive('lockForUpdate')->with('payer-id')->andReturn($senderLocked);
        $this->walletRepo->shouldReceive('lockForUpdate')->with('payee-id')->andReturn($receiverLocked);

        $transaction = new Transaction(['status' => TransactionEnum::COMPLETED->value]);
        $this->transactionRepo->shouldReceive('create')->once()->andReturn($transaction);

        $result = $this->service->execute('payer-id', 'payee-id', 50);

        $this->assertEquals(TransactionEnum::COMPLETED->value, $result->status);
        Queue::assertPushed(SendNotificationJob::class);
    }

    public function test_execute_throws_exception_when_value_is_zero_or_less()
    {
        $this->authService->shouldReceive('isAuthorized')->never();

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Value must be greater than zero.');

        $this->service->execute('payer-id', 'payee-id', 0);
    }

    public function test_execute_throws_exception_when_wallet_not_found()
    {
        $this->authService->shouldReceive('isAuthorized')->once()->andReturn(true);

        $this->walletRepo->shouldReceive('lockForUpdate')->with('payer-id')->andReturn(null);
        $this->walletRepo->shouldReceive('lockForUpdate')->with('payee-id')->andReturn($this->mockWallet('payee-id'));

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Wallet not found.');

        $this->service->execute('payer-id', 'payee-id', 50);
    }

    public function test_execute_throws_exception_when_balance_is_insufficient_inside_transaction()
    {
        $this->authService->shouldReceive('isAuthorized')->once()->andReturn(true);

        $senderLocked = $this->mockWallet('payer-id', $this->mockUser(true), 10);
        $receiverLocked = $this->mockWallet('payee-id');

        $this->walletRepo->shouldReceive('lockForUpdate')->with('payer-id')->andReturn($senderLocked);
        $this->walletRepo->shouldReceive('lockForUpdate')->with('payee-id')->andReturn($receiverLocked);

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Insufficient balance.');

        $this->service->execute('payer-id', 'payee-id', 50);
    }

    private function mockUser(bool $canSend = true): MockInterface
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('canSendMoney')->andReturn($canSend);

        $user->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $user->shouldReceive('getAttribute')->with('phone_number')->andReturn('123456789');
        $user->shouldReceive('getAttribute')->andReturnNull()->byDefault();

        return $user;
    }

    private function mockWallet(string $id, $owner = null, float $balance = 0): MockInterface
    {
        $wallet = Mockery::mock(Wallet::class);
        $wallet->shouldAllowMockingProtectedMethods();
        $wallet->shouldReceive('getAttribute')->with('id')->andReturn($id);

        if ($owner) {
            $wallet->shouldReceive('getAttribute')->with('owner')->andReturn($owner);
        }

        $wallet->shouldReceive('getAttribute')->with('balance')->andReturn($balance);

        return $wallet;
    }
}
