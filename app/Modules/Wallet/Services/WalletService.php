<?php

namespace App\Modules\Wallet\Services;

use App\Modules\Wallet\Repositories\WalletRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class WalletService
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository
    ) {
    }

    public function getWallet(string $id): ?\App\Modules\Wallet\Models\Wallet
    {
        return Cache::rememberForever("wallet_{$id}", function () use ($id) {
            return $this->walletRepository->findById($id);
        });
    }
}
