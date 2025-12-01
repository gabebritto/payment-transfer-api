<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepositoryInterface;
use App\Modules\Wallet\Models\Wallet;
use App\Modules\Wallet\Repositories\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly WalletRepositoryInterface $walletRepository,
    ) {
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $user = $this->userRepository->create($data);

            $wallet = new Wallet([
                'owner_type' => $user->getMorphClass(),
                'owner_id' => $user->getKey(),
                'balance' => $data['balance'] ?? 0,
            ]);

            $this->walletRepository->save($wallet);

            return $user;
        });
    }
}
