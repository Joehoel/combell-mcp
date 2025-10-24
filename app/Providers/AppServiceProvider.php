<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Joehoel\Combell\Combell;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Combell service to use request headers
        $this->app->bind(Combell::class, function ($app): Combell {
            $request = $app->make('request');

            // Get API credentials from request headers (set by middleware)
            $apiKey = $request->header('X-API-Key') ?? config('services.combell.api_key');
            $apiSecret = $request->header('X-API-Secret') ?? config('services.combell.api_secret');

            return new Combell($apiKey, $apiSecret);
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
