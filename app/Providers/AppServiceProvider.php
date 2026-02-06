<?php

namespace App\Providers;

use App\Policies\CreditPolicy;
use App\Src\CashOperation\Providers\CashOperationServiceProvider;
use App\Src\Client\Providers\ClientServiceProvider;
use App\Src\Collectors\Providers\CollectorServiceProvider;
use App\Src\Credits\Models\CreditsModel;
use App\Src\Credits\Providers\CreditServiceProvider;
use App\Src\Installments\Providers\InstallmentServiceProvider;
use App\Src\Payments\Providers\PaymentServiceProvider;
use App\Src\User\Providers\UserServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(UserServiceProvider::class);
        $this->app->register(ClientServiceProvider::class);
        $this->app->register(CreditServiceProvider::class);
        $this->app->register(InstallmentServiceProvider::class);
        $this->app->register(CashOperationServiceProvider::class);
        $this->app->register(PaymentServiceProvider::class);
        $this->app->register(CollectorServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(CreditsModel::class, CreditPolicy::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
