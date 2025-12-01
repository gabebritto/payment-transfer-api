<?php

namespace App\Modules\Wallet\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Wallet\Services\WalletService;
use Illuminate\Http\JsonResponse;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService
    ) {
    }

    public function show(string $walletId): JsonResponse
    {
        $wallet = $this->walletService->getWallet($walletId);

        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found'], 404);
        }

        return response()->json($wallet);
    }
}
