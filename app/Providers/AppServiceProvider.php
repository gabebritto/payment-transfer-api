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
            \App\Modules\Transfer\Repositories\TransactionRepositoryInterface::class,
            \App\Modules\Transfer\Repositories\TransactionRepository::class
        );
        $this->app->bind(
            \App\Modules\Wallet\Repositories\WalletRepositoryInterface::class,
            \App\Modules\Wallet\Repositories\WalletRepository::class
        );
        $this->app->bind(
            \App\Modules\User\Repositories\UserRepositoryInterface::class,
            \App\Modules\User\Repositories\UserRepository::class
        );
        $this->app->bind(
            \App\Modules\User\Repositories\RetailerRepositoryInterface::class,
            \App\Modules\User\Repositories\RetailerRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Modules\Wallet\Models\Wallet::observe(\App\Modules\Wallet\Observers\WalletObserver::class);
    }
}
