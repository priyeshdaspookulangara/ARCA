<?php

namespace Modules\AuthMgt\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Modules\AuthMgt\Application\Services\AuthServiceInterface;
use Modules\AuthMgt\Application\Services\AuthService;
use Modules\AuthMgt\Application\Services\PermissionServiceInterface;
use Modules\AuthMgt\Application\Services\PermissionService;

class AuthServiceProvider extends ServiceProvider
{
    protected $moduleName = 'AuthMgt';
    protected $moduleNameLower = 'authmgt';

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        $this->loadModuleRoutes();
    }

    public function register()
    {
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(PermissionServiceInterface::class, PermissionService::class);

        $this->app->singleton('authmgt', function ($app) {
            return $app->make(AuthServiceInterface::class);
        });
    }

    protected function registerConfig()
    {
        $configPath = module_path($this->moduleName, 'config/config.php');
        if (File::exists($configPath)) {
            $this->publishes([
                $configPath => config_path($this->moduleNameLower . '.php'),
            ], 'config');
            $this->mergeConfigFrom($configPath, $this->moduleNameLower);
        }
    }

    protected function loadModuleRoutes()
    {
        $mainModuleRoutePath = module_path($this->moduleName, 'routes');

        if (File::exists($mainModuleRoutePath . '/api.php')) {
            Route::prefix('api/authmgt')
                ->middleware('api')
                ->namespace("Modules\AuthMgt\Http\Controllers")
                ->group($mainModuleRoutePath . '/api.php');
        }
    }

    public function provides()
    {
        return ['authmgt', AuthServiceInterface::class];
    }
}