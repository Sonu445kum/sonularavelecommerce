<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        //  Enable Bootstrap style pagination
        
        Paginator::useTailwind();

        //  (Optional) You can also set default string length for older MySQL versions
        // Schema::defaultStringLength(191);
    }
}
