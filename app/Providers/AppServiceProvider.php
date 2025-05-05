<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\TickerServiceInterface;
use App\Services\BinanceService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TickerServiceInterface::class, BinanceService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
