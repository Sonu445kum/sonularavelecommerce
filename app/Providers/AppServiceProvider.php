<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

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
        // Enable Tailwind pagination
        Paginator::useTailwind();

        // Force HTTPS in production (important for Render)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}