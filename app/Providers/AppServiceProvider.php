<?php

namespace App\Providers;

use App\Services\Crawler\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RateLimiter::class, fn () =>
            new RateLimiter(
                maxRequests: 3,
                windowSeconds: 10
            )
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
