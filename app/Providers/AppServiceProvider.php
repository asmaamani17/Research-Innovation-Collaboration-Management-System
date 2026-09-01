<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // Support both HTTP and HTTPS protocols
        if (in_array(config('app.env'), ['production', 'staging'])) {
            URL::forceScheme('https');
        }
    }
}
