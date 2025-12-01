<?php

use Illuminate\Support\Facades\Route;

Route::post('/transfer', [\App\Modules\Transfer\Controllers\TransferController::class, 'store']);
Route::get('/wallet/{id}', [\App\Modules\Wallet\Controllers\WalletController::class, 'show']);
