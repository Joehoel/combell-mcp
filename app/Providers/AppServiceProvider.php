<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Joehoel\Combell\Combell;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register singleton for Combell
        $this->app->singleton(Combell::class, function ($app) {
            return new Combell(
                config("services.combell.api_key"),
                config("services.combell.api_secret"),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
