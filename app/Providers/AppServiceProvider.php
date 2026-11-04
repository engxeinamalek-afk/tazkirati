<?php

namespace App\Providers;

use App\Services\SMSProvider\FakeSMSProvider;
use App\Services\SMSProvider\SMSProviderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
