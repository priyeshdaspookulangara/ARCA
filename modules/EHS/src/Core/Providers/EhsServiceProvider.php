<?php

namespace Modules\EHS\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class EhsServiceProvider extends ServiceProvider
{
    protected $moduleName = 'EHS';
    protected $moduleNameLower = 'ehs';

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        // $this->registerViews(); // Uncomment if EHS has Blade views
        $this->registerTranslations(); // EHS likely has specific terminology
        $this->loadModuleRoutes();
    }

    public function register()
    {
        $this->app->singleton('ehs', function ($app) {
            return new \stdClass(); // Placeholder for a core EHS service
        });

        // Further service registrations for EHS domains
    }

    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower
        );
    }

    protected function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        $this->publishes([
            module_path($this->moduleName, 'resources/lang') => $langPath
        ], ['lang', $this->moduleNameLower . '-module-translations']);

        $this->loadTranslationsFrom(module_path($this->moduleName, 'resources/lang'), $this->moduleNameLower);
    }

    protected function loadModuleRoutes()
    {
        $mainModuleRoutePath = module_path($this->moduleName, 'routes');

        if (File::exists($mainModuleRoutePath . '/web.php')) {
            Route::middleware('web')
                ->namespace("Modules\\EHS\\Core\\Http\\Controllers") // Adjust if needed
                ->group($mainModuleRoutePath . '/web.php');
        }
        if (File::exists($mainModuleRoutePath . '/api.php')) {
            Route::prefix('api/ehs') // Specific API prefix for EHS
                ->middleware(['api', 'auth:sanctum']) // Secure EHS APIs
                ->namespace("Modules\\EHS\\Core\\Http\\Controllers") // Adjust if needed
                ->group($mainModuleRoutePath . '/api.php');
        }
    }

    public function provides()
    {
        return ['ehs'];
    }
}