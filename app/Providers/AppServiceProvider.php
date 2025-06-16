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
        // Example: Register HR Module Service Provider if it exists
        if (class_exists(\Modules\HR\Providers\HRServiceProvider::class)) {
            $this->app->register(\Modules\HR\Providers\HRServiceProvider::class);
        }

        // Register Fina Module Service Provider if it exists and is enabled (config based check would be better)
        if (class_exists(\Modules\Fina\Core\Providers\FinaServiceProvider::class)) {
            $this->app->register(\Modules\Fina\Core\Providers\FinaServiceProvider::class);
        }

        // Future: Dynamically scan 'modules' directory and register providers
        // based on a configuration or manifest file for each module.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
