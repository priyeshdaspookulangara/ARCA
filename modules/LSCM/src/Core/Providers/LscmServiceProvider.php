<?php

namespace Modules\LSCM\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class LscmServiceProvider extends ServiceProvider
{
    protected $moduleName = 'LSCM';
    protected $moduleNameLower = 'lscm';
    protected $subModules = ['MM', 'SD', 'PP', 'PM', 'QM']; // Define LSCM sub-modules

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        // Views and translations can be registered similarly if needed globally for LSCM
        // $this->registerViews();
        // $this->registerTranslations();

        $this->loadModuleRoutes();
    }

    public function register()
    {
        $this->app->singleton('lscm', function ($app) {
            return new \stdClass(); // Placeholder for a core LSCM service
        });

        // Conditionally register services for enabled sub-modules
        // foreach ($this->subModules as $subModule) {
        //     if (config('lscm.' . strtolower($subModule) . '.enabled', false)) {
        //         // Register sub-module specific providers or services
        //         // e.g., $this->app->register("Modules\LSCM\{$subModule}\Providers\{$subModule}ServiceProvider::class");
        //     }
        // }
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
        $mainModulePath = module_path($this->moduleName, 'routes');
        $srcModulePathBase = module_path($this->moduleName, 'src');

        // Load main LSCM routes
        if (File::exists($mainModulePath . '/web.php')) {
            Route::middleware('web')
                ->namespace("Modules\LSCM\Core\Http\Controllers") // Adjust default namespace if LSCM Core has controllers
                ->group($mainModulePath . '/web.php');
        }
        if (File::exists($mainModulePath . '/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->namespace("Modules\LSCM\Core\Http\Controllers") // Adjust default namespace
                ->group($mainModulePath . '/api.php');
        }

        // Load routes for active sub-modules
        // foreach ($this->subModules as $subModule) {
        //     if (config('lscm.' . strtolower($subModule) . '.enabled', false)) {
        //         $subModuleRoutesPath = $srcModulePathBase . "/{$subModule}/routes";
        //         if (File::exists($subModuleRoutesPath . '/web.php')) {
        //             Route::middleware('web')
        //                 ->prefix(strtolower($this->moduleNameLower) . '/' . strtolower($subModule))
        //                 ->namespace("Modules\LSCM\{$subModule}\Http\Controllers")
        //                 ->group($subModuleRoutesPath . '/web.php');
        //         }
        //         if (File::exists($subModuleRoutesPath . '/api.php')) {
        //             Route::prefix('api/' . strtolower($this->moduleNameLower) . '/' . strtolower($subModule))
        //                 ->middleware('api')
        //                 ->namespace("Modules\LSCM\{$subModule}\Http\Controllers")
        //                 ->group($subModuleRoutesPath . '/api.php');
        //         }
        //     }
        // }
    }

    // Placeholder for registerViews and registerTranslations if LSCM needs global ones
    // public function registerViews() { /* ... */ }
    // public function registerTranslations() { /* ... */ }


    public function provides()
    {
        return ['lscm'];
    }
}
