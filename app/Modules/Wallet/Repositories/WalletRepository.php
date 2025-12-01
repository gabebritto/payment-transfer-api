<?php

namespace App\Modules\Wallet\Repositories;

use App\Modules\Wallet\Models\Wallet;

class WalletRepository implements WalletRepositoryInterface
{
    public function findById(string $walletId): ?Wallet
    {
        return Wallet::find($walletId);
    }

    public function lockForUpdate(string $walletId): ?Wallet
    {
        return Wallet::with('owner')->lockForUpdate()->find($walletId);
    }

    public function save(Wallet $wallet): bool
    {
        return $wallet->save();
    }
}
