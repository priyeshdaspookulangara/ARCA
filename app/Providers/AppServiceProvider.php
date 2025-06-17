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

        // Register CRM Module Service Provider if it exists and is enabled
        if (class_exists(\Modules\CRM\Core\Providers\CrmServiceProvider::class)) {
            $this->app->register(\Modules\CRM\Core\Providers\CrmServiceProvider::class);
        }

        // Register LSCM Module Service Provider if it exists and is enabled
        if (class_exists(\Modules\LSCM\Core\Providers\LscmServiceProvider::class)) {
            $this->app->register(\Modules\LSCM\Core\Providers\LscmServiceProvider::class);
        }

        // Register PS Module Service Provider if it exists and is enabled
        if (class_exists(\Modules\PS\Core\Providers\PsServiceProvider::class)) {
            $this->app->register(\Modules\PS\Core\Providers\PsServiceProvider::class);
        }

        // Register EWM Module Service Provider if it exists and is enabled
        if (class_exists(\Modules\EWM\Core\Providers\EwmServiceProvider::class)) {
            $this->app->register(\Modules\EWM\Core\Providers\EwmServiceProvider::class);
        }

        // Register AuthMgt Module Service Provider if it exists and is enabled
        if (class_exists(\Modules\AuthMgt\Core\Providers\AuthServiceProvider::class)) {
            $this->app->register(\Modules\AuthMgt\Core\Providers\AuthServiceProvider::class);
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
