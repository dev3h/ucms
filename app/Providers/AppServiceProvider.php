<?php

namespace App\Providers;

use App\Models\Action;
use App\Models\Module;
use App\Models\SubSystem;
use App\Models\System;
use App\Observers\ActionObserver;
use App\Observers\ModuleObserver;
use App\Observers\PermissionObserver;
use App\Observers\SubSystemObserver;
use App\Observers\SystemObserver;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use App\Services\ActivityLoggerService;
use Spatie\Activitylog\ActivityLogger as SpatieActivityLogger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \Laravel\Fortify\Fortify::ignoreRoutes();

        $this->app->singleton(SpatieActivityLogger::class, ActivityLoggerService::class);
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

        // observer
        System::observe(SystemObserver::class);
        SubSystem::observe(SubSystemObserver::class);
        Module::observe(ModuleObserver::class);
        Action::observe(ActionObserver::class);
        Permission::observe(PermissionObserver::class);
    }
}
