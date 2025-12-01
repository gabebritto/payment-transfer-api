<?php

namespace App\Modules\Transfer\Services;

use App\Modules\Transfer\Enums\TransactionEnum;
use App\Modules\Transfer\Exceptions\TransactionException;
use App\Modules\Transfer\Jobs\SendNotificationJob;
use App\Modules\Transfer\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function __construct(
        private readonly AuthorizationService $authorizationService,
        private readonly \App\Modules\Wallet\Repositories\WalletRepositoryInterface $walletRepository,
        private readonly \App\Modules\Transfer\Repositories\TransactionRepositoryInterface $transactionRepository
    ) {}

    public function execute(string $payerId, string $payeeId, int $value): Transaction
    {
        // Basic validations (Fail Fast)
        if ($payerId === $payeeId) {
            throw TransactionException::sameWalletTransfer();
        }

        if ($value <= 0) {
            throw TransactionException::valueMustBeGreaterThanZero();
        }

        // Consult Authorization Service
        if (! $this->authorizationService->isAuthorized()) {
            throw TransactionException::unauthorizedTransfer();
        }

        // DB Transaction
        return DB::transaction(function () use ($payerId, $payeeId, $value) {
            $firstId = ($payerId < $payeeId) ? $payerId : $payeeId;
            $secondId = ($payerId < $payeeId) ? $payeeId : $payerId;

            $walletA = $this->walletRepository->lockForUpdate($firstId);
            $walletB = $this->walletRepository->lockForUpdate($secondId);

            if (! $walletA || ! $walletB) {
                throw TransactionException::walletNotFound();
            }

            $sender = ($walletA->id == $payerId) ? $walletA : $walletB;
            $receiver = ($walletA->id == $payeeId) ? $walletA : $walletB;

            if (! $sender->owner->canSendMoney()) {
                throw TransactionException::payerCannotSendMoney();
            }

            if ($sender->balance < $value) {
                throw TransactionException::insufficientBalance();
            }

            // Execute transference
            $sender->decrement('balance', $value);
            $receiver->increment('balance', $value);

            // Record Transaction
            $transaction = $this->transactionRepository->create([
                'payer_id' => $sender->owner->id,
                'payer_type' => $sender->owner::class,
                'payee_id' => $receiver->owner->id,
                'payee_type' => $receiver->owner::class,
                'value' => $value,
                'status' => TransactionEnum::COMPLETED->value,
            ]);

            // Async Notification
            SendNotificationJob::dispatch(
                $receiver->owner->email,
                $receiver->owner->phone_number,
                "You received a payment of {$value}"
            )->afterCommit();

            return $transaction;
        });
    }
}
