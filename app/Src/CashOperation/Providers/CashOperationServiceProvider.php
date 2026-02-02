<?php

namespace App\Src\CashOperation\Providers;

use Illuminate\Support\ServiceProvider;

class CashOperationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(
            base_path('app/Src/CashOperation/Migrations')
        );
    }
}
