<?php

namespace Modules\MDG\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class MdgServiceProvider extends ServiceProvider
{
    protected \$moduleName = 'MDG';
    protected \$moduleNameLower = 'mdg';

    public function boot()
    {
        \$this->registerConfig();
        \$this->loadMigrationsFrom(module_path(\$this->moduleName, 'database/migrations'));
        \$this->registerTranslations();
        \$this->loadModuleRoutes();
    }

    public function register()
    {
        \$this->app->singleton('mdg', function (\$app) {
            return new \stdClass(); // Placeholder for a core MDG service facade
        });

        // Register Application Services, Repositories for each MDG domain
        // e.g., \$this->app->bind(ChangeRequestRepositoryInterface::class, EloquentChangeRequestRepository::class);
        // \$this->app->bind(WorkflowEngineInterface::class, SymfonyWorkflowAdapter::class); // Example
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

    protected function registerTranslations()
    {
        \$langPath = resource_path('lang/modules/' . \$this->moduleNameLower);

        \$this->publishes([
            module_path(\$this->moduleName, 'resources/lang') => \$langPath
        ], ['lang', \$this->moduleNameLower . '-module-translations']);

        \$this->loadTranslationsFrom(module_path(\$this->moduleName, 'resources/lang'), \$this->moduleNameLower);
    }

    protected function loadModuleRoutes()
    {
        \$mainModuleRoutePath = module_path(\$this->moduleName, 'routes');

        if (File::exists(\$mainModuleRoutePath . '/api.php')) {
            Route::prefix('api/mdg')
                ->middleware(['api', 'auth:sanctum']) // Secure MDG APIs
                ->namespace("Modules\\MDG\\Core\\Http\\Controllers") // Default for core MDG routes if any
                ->group(\$mainModuleRoutePath . '/api.php');
        }

        // Web routes if MDG has any direct web UI not part of SPA
        // if (File::exists(\$mainModuleRoutePath . '/web.php')) {
        //     Route::middleware('web')
        //         ->namespace("Modules\\MDG\\Core\\Http\\Controllers")
        //         ->group(\$mainModuleRoutePath . '/web.php');
        // }
    }

    public function provides()
    {
        return ['mdg'];
    }
}
