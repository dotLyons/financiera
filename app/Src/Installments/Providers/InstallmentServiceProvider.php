<?php

namespace App\Src\Installments\Providers;

use Illuminate\Support\ServiceProvider;

class InstallmentServiceProvider extends ServiceProvider
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
            base_path('app/Src/Installments/Migrations')
        );
    }
}
