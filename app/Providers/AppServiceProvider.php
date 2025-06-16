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
