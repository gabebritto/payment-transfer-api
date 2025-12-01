<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Wallet\Repositories\WalletRepositoryInterface::class,
            \App\Modules\Wallet\Repositories\WalletRepository::class
        );

        $this->app->bind(
            \App\Modules\Transfer\Repositories\TransactionRepositoryInterface::class,
            \App\Modules\Transfer\Repositories\TransactionRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
