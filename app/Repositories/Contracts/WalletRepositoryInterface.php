<?php

namespace App\Repositories\Contracts;

use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function findById(string $id): ?Wallet;

    public function lockForUpdate(string $id): ?Wallet;

    public function save(Wallet $wallet): bool;
}
