<?php

namespace App\Modules\Wallet\Repositories;

use App\Modules\Wallet\Models\Wallet;

interface WalletRepositoryInterface
{
    public function findById(string $id): ?Wallet;

    public function lockForUpdate(string $id): ?Wallet;

    public function save(Wallet $wallet): bool;
}
