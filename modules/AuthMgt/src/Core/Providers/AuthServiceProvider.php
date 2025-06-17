<?php

namespace Modules\AuthMgt\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File; // For File::exists
use Illuminate\Support\Facades\Gate; // For registering policies if any are module-specific beyond global ones
use Modules\AuthMgt\Authorization\Application\AuthorizationServiceInterface;
use Modules\AuthMgt\Authorization\Application\AuthorizationService;


class AuthServiceProvider extends ServiceProvider
{
    protected $moduleName = 'AuthMgt';
    protected $moduleNameLower = 'authmgt'; // Used for config file name typically

    // Define policies if AuthMgt manages entities that need policies themselves
    // protected $policies = [
    //     // 'Modules\AuthMgt\SomeModel' => 'Modules\AuthMgt\Policies\SomeModelPolicy',
    // ];

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        // $this->registerPolicies(); // Call policy registration
        // $this->registerViews();
        // $this->registerTranslations();
        $this->loadModuleRoutes();
    }

    public function register()
    {
        $this->app->singleton(AuthorizationServiceInterface::class, AuthorizationService::class);

        $this->app->singleton('authmgt', function ($app) {
            // This could be a facade or a central service for the module if needed
            return new \stdClass(); // Placeholder
        });

        // Register other core services for UserManagement, RoleManagement etc.
        // e.g., $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
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

    // Method to register policies
    // public function registerPolicies()
    // {
    //     foreach ($this->policies as $model => $policy) {
    //         Gate::policy($model, $policy);
    //     }
    // }

    protected function loadModuleRoutes()
    {
        $mainModuleRoutePath = module_path($this->moduleName, 'routes');

        // API routes are primary for this module
        if (File::exists($mainModuleRoutePath . '/api.php')) {
            Route::prefix('api/admin/auth') // Prefix for admin auth routes
                ->middleware(['api', 'auth:sanctum']) // Secure with appropriate auth middleware
                ->namespace("Modules\AuthMgt\Http\Controllers") // Assuming a general Http dir for core routes
                ->group($mainModuleRoutePath . '/api.php');
        }

        // Potentially some web routes for things like password reset UI if not handled by main app
        // if (File::exists($mainModuleRoutePath . '/web.php')) {
        //     Route::middleware('web')
        //         ->namespace("Modules\AuthMgt\Http\Controllers")
        //         ->group($mainModuleRoutePath . '/web.php');
        // }
    }

    public function provides()
    {
        return ['authmgt', AuthorizationServiceInterface::class];
    }
}
