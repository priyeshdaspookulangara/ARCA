<?php

namespace Modules\PLM\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class PlmServiceProvider extends ServiceProvider
{
    protected \$moduleName = 'PLM';
    protected \$moduleNameLower = 'plm';

    public function boot()
    {
        \$this->registerConfig();
        \$this->loadMigrationsFrom(module_path(\$this->moduleName, 'database/migrations'));
        // \$this->registerViews(); // Uncomment if PLM has Blade views
        // \$this->registerTranslations(); // Uncomment if PLM has specific lang files
        \$this->loadModuleRoutes();
    }

    public function register()
    {
        \$this->app->singleton('plm', function (\$app) {
            return new \stdClass(); // Placeholder for a core PLM service
        });

        // Further service registrations for PLM domains (PDM, BOM, ChangeMgt, etc.)
    }

    protected function registerConfig()
    {
        \$this->publishes([
            module_path(\$this->moduleName, 'config/config.php') => config_path(\$this->moduleNameLower . '.php'),
        ], 'config');
        \$this->mergeConfigFrom(
            module_path(\$this->moduleName, 'config/config.php'), \$this->moduleNameLower
        );
    }

    protected function loadModuleRoutes()
    {
        \$mainModuleRoutePath = module_path(\$this->moduleName, 'routes');

        if (File::exists(\$mainModuleRoutePath . '/web.php')) {
            Route::middleware('web')
                ->namespace("Modules\\PLM\\Core\\Http\\Controllers") // Adjust if needed
                ->group(\$mainModuleRoutePath . '/web.php');
        }
        if (File::exists(\$mainModuleRoutePath . '/api.php')) {
            Route::prefix('api/plm') // Specific API prefix for PLM
                ->middleware(['api', 'auth:sanctum']) // Secure PLM APIs
                ->namespace("Modules\\PLM\\Core\\Http\\Controllers") // Adjust if needed
                ->group(\$mainModuleRoutePath . '/api.php');
        }

        // Potentially load routes from sub-domains like PDM, BOM, ChangeMgt if they have their own route files
        // Example for PDM:
        // \$pdmRoutesPath = module_path(\$this->moduleName, 'src/PDM/Http/routes.php');
        // if (File::exists(\$pdmRoutesPath)) {
        //     Route::prefix('api/plm/pdm')
        //         ->middleware(['api', 'auth:sanctum'])
        //         ->namespace("Modules\\PLM\\PDM\\Http\\Controllers")
        //         ->group(\$pdmRoutesPath);
        // }
    }

    public function provides()
    {
        return ['plm'];
    }
}
