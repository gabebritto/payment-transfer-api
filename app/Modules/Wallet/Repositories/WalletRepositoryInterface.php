<?php

namespace App\Modules\Wallet\Repositories;

use App\Modules\Wallet\Models\Wallet;

interface WalletRepositoryInterface
{
    public function findById(string $walletId): ?Wallet;

    public function lockForUpdate(string $walletId): ?Wallet;

    public function save(Wallet $wallet): bool;
}
