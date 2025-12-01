<?php

namespace App\Repositories\Eloquent;

use App\Models\Wallet;
use App\Repositories\Contracts\WalletRepositoryInterface;

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
