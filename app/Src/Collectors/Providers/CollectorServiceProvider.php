<?php

namespace App\Src\Collectors\Providers;

use Illuminate\Support\ServiceProvider;

class CollectorServiceProvider extends ServiceProvider
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
            base_path('app/Src/Collectors/Migrations')
        );
    }
}
