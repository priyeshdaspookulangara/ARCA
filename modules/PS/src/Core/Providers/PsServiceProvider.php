<?php

namespace Modules\PS\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class PsServiceProvider extends ServiceProvider
{
    protected $moduleName = 'PS';
    protected $moduleNameLower = 'ps';
    // Define PS functional domains that might have their own routes/configs if needed
    protected $functionalDomains = ['Structuring', 'Scheduling', 'Costing', 'ResourceMgt', 'MaterialMgt', 'Execution', 'Closing'];

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        // $this->registerViews();
        // $this->registerTranslations();
        $this->loadModuleRoutes();
    }

    public function register()
    {
        $this->app->singleton('ps', function ($app) {
            return new \stdClass(); // Placeholder for a core PS service
        });

        // Example: Conditionally register services for enabled functional domains
        // foreach ($this->functionalDomains as $domain) {
        //     if (config('ps.' . strtolower($domain) . '.enabled', true)) { // Assuming enabled by default
        //         // Register domain-specific providers or services
        //         // e.g., $this->app->register("Modules\PS\{$domain}\Providers\{$domain}ServiceProvider::class");
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
        $mainModuleRoutePath = module_path($this->moduleName, 'routes');

        // Load main PS routes
        if (File::exists($mainModuleRoutePath . '/web.php')) {
            Route::middleware('web')
                ->namespace("Modules\PS\Core\Http\Controllers") // Default namespace for core PS routes
                ->group($mainModuleRoutePath . '/web.php');
        }
        if (File::exists($mainModuleRoutePath . '/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->namespace("Modules\PS\Core\Http\Controllers") // Default namespace
                ->group($mainModuleRoutePath . '/api.php');
        }

        // Example: Load routes for functional domains if they have their own route files
        // foreach ($this->functionalDomains as $domain) {
        //     if (config('ps.' . strtolower($domain) . '.enabled', true)) {
        //         $domainRoutesPath = module_path($this->moduleName, "src/{$domain}/Http/routes.php"); // Assuming routes in Http dir
        //         if (File::exists($domainRoutesPath)) {
        //             Route::prefix('api/' . $this->moduleNameLower . '/' . strtolower($domain))
        //                 ->middleware('api')
        //                 ->namespace("Modules\PS\{$domain}\Http\Controllers")
        //                 ->group($domainRoutesPath);
        //         }
        //     }
        // }
    }

    public function provides()
    {
        return ['ps'];
    }
}
