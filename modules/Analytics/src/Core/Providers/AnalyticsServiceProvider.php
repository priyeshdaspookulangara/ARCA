<?php

namespace Modules\Analytics\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class AnalyticsServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Analytics';
    protected $moduleNameLower = 'analytics';

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        $this->loadModuleRoutes();
    }

    public function register()
    {
        $this->app->singleton('analytics', function ($app) {
            return new \stdClass(); // Placeholder for a core Analytics service facade
        });

        $this->app->bind(
            \Modules\Analytics\Dimensions\Domain\DimCustomerRepositoryInterface::class,
            \Modules\Analytics\Dimensions\Infrastructure\Persistence\EloquentDimCustomerRepository::class
        );
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

        if (File::exists($mainModuleRoutePath . '/api.php')) {
            Route::prefix('api/analytics')
                ->middleware(['api', 'auth:sanctum'])
                ->namespace("Modules\\Analytics\\Core\\Http\\Controllers")
                ->group($mainModuleRoutePath . '/api.php');
        }
    }

    public function provides()
    {
        return ['analytics'];
    }
}