<?php

namespace App\Modules\Transfer\Services;

use App\Modules\Transfer\Enums\TransactionEnum;
use App\Modules\Transfer\Exceptions\TransactionException;
use App\Modules\Transfer\Jobs\SendNotificationJob;
use App\Modules\Transfer\Models\Transaction;
use App\Modules\Transfer\Repositories\TransactionRepositoryInterface;
use App\Modules\Wallet\Models\Wallet;
use App\Modules\Wallet\Repositories\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferService
{
    public function __construct(
        private readonly AuthorizationService $authorizationService,
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly TransactionRepositoryInterface $transactionRepository
    ) {}

    public function execute(string $payerId, string $payeeId, int $value): Transaction
    {
        Log::info('Transfer initiated', [
            'payer_id' => $payerId,
            'payee_id' => $payeeId,
            'value' => $value,
        ]);

        $this->validateSelfTransfer($payerId, $payeeId);
        $this->validateAmount($value);
        $this->validateAuthorization();

        return DB::transaction(function () use ($payerId, $payeeId, $value) {
            [$walletA, $walletB] = $this->lockWallets($payerId, $payeeId);

            $sender = ($walletA->id == $payerId) ? $walletA : $walletB;
            $receiver = ($walletA->id == $payeeId) ? $walletA : $walletB;

            $this->validatePayerType($sender);
            $this->validateBalance($sender, $value);

            $this->performTransfer($sender, $receiver, $value);

            $transaction = $this->recordTransaction($sender, $receiver, $value);

            $this->dispatchNotification($receiver, $value);

            return $transaction;
        });
    }

    private function validateSelfTransfer(string $payerId, string $payeeId): void
    {
        if ($payerId === $payeeId) {
            Log::error('Transfer failed: Payer and Payee are the same', ['payer_id' => $payerId]);
            throw TransactionException::sameWalletTransfer();
        }
    }

    private function validateAmount(int $value): void
    {
        if ($value <= 0) {
            Log::error('Transfer failed: Value must be greater than zero', ['value' => $value]);
            throw TransactionException::valueMustBeGreaterThanZero();
        }
    }

    private function validateAuthorization(): void
    {
        if (! $this->authorizationService->isAuthorized()) {
            Log::error('Transfer failed: Unauthorized by external service');
            throw TransactionException::unauthorizedTransfer();
        }
    }

    private function lockWallets(string $payerId, string $payeeId): array
    {
        $firstId = ($payerId < $payeeId) ? $payerId : $payeeId;
        $secondId = ($payerId < $payeeId) ? $payeeId : $payerId;

        $walletA = $this->walletRepository->lockForUpdate($firstId);
        $walletB = $this->walletRepository->lockForUpdate($secondId);

        if (! $walletA || ! $walletB) {
            throw TransactionException::walletNotFound();
        }

        return [$walletA, $walletB];
    }

    private function validatePayerType(Wallet $sender): void
    {
        if (! $sender->owner->canSendMoney()) {
            throw TransactionException::payerCannotSendMoney();
        }
    }

    private function validateBalance(Wallet $sender, int $value): void
    {
        if ($sender->balance < $value) {
            throw TransactionException::insufficientBalance();
        }
    }

    private function performTransfer(Wallet $sender, Wallet $receiver, int $value): void
    {
        $sender->decrement('balance', $value);
        $receiver->increment('balance', $value);
    }

    private function recordTransaction(Wallet $sender, Wallet $receiver, int $value): Transaction
    {
        return $this->transactionRepository->create([
            'payer_id' => $sender->owner->id,
            'payer_type' => $sender->owner::class,
            'payee_id' => $receiver->owner->id,
            'payee_type' => $receiver->owner::class,
            'value' => $value,
            'status' => TransactionEnum::COMPLETED->value,
        ]);
    }

    private function dispatchNotification(Wallet $receiver, int $value): void
    {
        SendNotificationJob::dispatch(
            $receiver->owner->email,
            $receiver->owner->phone_number,
            "You received a payment of {$value}"
        )->afterCommit();

        Log::info('Notification job dispatched');
    }
}
