<?php

use App\Modules\Transfer\Controllers\TransferController;
use App\Modules\User\Http\Controllers\RetailerController;
use App\Modules\User\Http\Controllers\UserController;
use App\Modules\Wallet\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::post('/transfer', [TransferController::class, 'store']);
Route::get('/wallet/{walletId}', [WalletController::class, 'show']);

Route::post('/users', [UserController::class, 'store']);
Route::post('/retailers', [RetailerController::class, 'store']);
