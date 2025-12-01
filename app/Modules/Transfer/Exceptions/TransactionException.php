<?php

namespace App\Modules\Transfer\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class TransactionException extends Exception
{
    public static function walletNotFound(): self
    {
        return new self('Wallet not found.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function payerCannotSendMoney(): self
    {
        return new self('Payer cannot send money.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function sameWalletTransfer(): self
    {
        return new self('Cannot transfer to same wallet.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function insufficientBalance(): self
    {
        return new self('Insufficient balance.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function unauthorizedTransfer(): self
    {
        return new self('Transfer not authorized by external service.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function valueMustBeGreaterThanZero(): self
    {
        return new self('Value must be greater than zero.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
