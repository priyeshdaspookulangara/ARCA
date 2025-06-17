<?php

namespace Modules\EWM\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class EwmServiceProvider extends ServiceProvider
{
    protected $moduleName = 'EWM';
    protected $moduleNameLower = 'ewm';

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        // $this->registerViews(); // Uncomment if EWM has Blade views
        // $this->registerTranslations(); // Uncomment if EWM has specific lang files outside default 'en'
        $this->loadModuleRoutes();
    }

    public function register()
    {
        $this->app->singleton('ewm', function ($app) {
            return new \stdClass(); // Placeholder for a core EWM service
        });

        // Further service registrations for EWM domains can be added here
        // or in dedicated service providers per EWM sub-domain if complexity grows.
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

    protected function loadModuleRoutes()
    {
        $mainModuleRoutePath = module_path($this->moduleName, 'routes');

        if (File::exists($mainModuleRoutePath . '/web.php')) {
            Route::middleware('web')
                // Consider a more specific namespace if Core/Http/Controllers is too generic
                ->namespace("Modules\EWM\Core\Http\Controllers")
                ->group($mainModuleRoutePath . '/web.php');
        }
        if (File::exists($mainModuleRoutePath . '/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                 // Consider a more specific namespace
                ->namespace("Modules\EWM\Core\Http\Controllers")
                ->group($mainModuleRoutePath . '/api.php');
        }

        // RF routes could be specifically namespaced and prefixed
        $rfRoutesPath = module_path($this->moduleName, 'src/RF/Http/routes.php');
        if (File::exists($rfRoutesPath)) {
             Route::prefix('api/ewm/rf') // Specific prefix for RF APIs
                ->middleware(['api', 'auth:sanctum']) // Ensure RF routes are authenticated
                ->namespace("Modules\EWM\RF\Http\Controllers")
                ->group($rfRoutesPath);
        }
    }

    public function provides()
    {
        return ['ewm'];
    }
}
