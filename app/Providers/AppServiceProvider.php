<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \Laravel\Fortify\Fortify::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // register macros
        $macroDirectory = __DIR__ . '/../Macros';
        $filesPath = glob($macroDirectory . '/*.php');

        foreach ($filesPath as $filePath) {
            require_once $filePath;
        }
    }
}
