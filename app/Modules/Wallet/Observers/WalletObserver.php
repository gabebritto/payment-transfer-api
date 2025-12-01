<?php

namespace App\Modules\Wallet\Observers;

use App\Modules\Wallet\Models\Wallet;
use Illuminate\Support\Facades\Cache;

class WalletObserver
{
    public function updated(Wallet $wallet): void
    {
        if ($wallet->isDirty('balance')) {
            Cache::forget("wallet_{$wallet->id}");
        }
    }
}
