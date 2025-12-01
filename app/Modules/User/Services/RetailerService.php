<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\Retailer;
use App\Modules\User\Repositories\RetailerRepositoryInterface;
use App\Modules\Wallet\Models\Wallet;
use App\Modules\Wallet\Repositories\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RetailerService
{
    public function __construct(
        private readonly RetailerRepositoryInterface $retailerRepository,
        private readonly WalletRepositoryInterface $walletRepository,
    ) {}

    public function create(array $data): Retailer
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);

            $retailer = $this->retailerRepository->create($data);

            $wallet = new Wallet([
                'owner_type' => $retailer->getMorphClass(),
                'owner_id' => $retailer->getKey(),
                'balance' => $data['balance'] ?? 0,
            ]);

            $this->walletRepository->save($wallet);

            return $retailer;
        });
    }
}
