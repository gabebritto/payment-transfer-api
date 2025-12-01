<?php

namespace App\Modules\Wallet\Repositories;

use App\Modules\Wallet\Models\Wallet;

class WalletRepository implements WalletRepositoryInterface
{
    public function findById(string $id): ?Wallet
    {
        return Wallet::find($id);
    }

    public function lockForUpdate(string $id): ?Wallet
    {
        return Wallet::with('owner')->lockForUpdate()->find($id);
    }

    public function save(Wallet $wallet): bool
    {
        return $wallet->save();
    }
}
