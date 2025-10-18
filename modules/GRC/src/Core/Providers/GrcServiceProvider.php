<?php

namespace Modules\GRC\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class GrcServiceProvider extends ServiceProvider
{
    protected $moduleName = 'GRC';
    protected $moduleNameLower = 'grc';

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        $this->registerTranslations();
        $this->loadModuleRoutes();
        // $this->registerViews(); // If GRC has Blade views for admin UIs
    }

    public function register()
    {
        $this->app->singleton('grc', function ($app) {
            return new \stdClass(); // Placeholder for a core GRC service facade
        });

        // Register Application Services, Repositories for each GRC pillar
        $this->app->bind(
            \Modules\GRC\AccessControl\Domain\RoleRepositoryInterface::class,
            \Modules\GRC\AccessControl\Infrastructure\Persistence\EloquentRoleRepository::class
        );
        $this->app->bind(
            \Modules\GRC\AccessControl\Domain\PermissionRepositoryInterface::class,
            \Modules\GRC\AccessControl\Infrastructure\Persistence\EloquentPermissionRepository::class
        );

        $this->app->bind(
            \Modules\GRC\AuditMgt\Domain\AuditLogRepositoryInterface::class,
            \Modules\GRC\AuditMgt\Infrastructure\Persistence\EloquentAuditLogRepository::class
        );

        $this->app->bind(
            \Modules\GRC\ProcessControl\Domain\SoDRuleRepositoryInterface::class,
            \Modules\GRC\ProcessControl\Infrastructure\Persistence\EloquentSoDRuleRepository::class
        );

        $this->app->bind(
            \Modules\GRC\ComplianceMgt\Domain\ConsentRepositoryInterface::class,
            \Modules\GRC\ComplianceMgt\Infrastructure\Persistence\EloquentConsentRepository::class
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

        // GRC routes are typically admin-focused and API-driven for a SPA frontend
        if (File::exists($mainModuleRoutePath . '/api.php')) {
            Route::prefix('api/grc')
                ->middleware(['api', 'auth:sanctum']) // Secure GRC APIs
                ->namespace("Modules\\GRC\\Core\\Http\\Controllers") // Default for core GRC routes
                ->group($mainModuleRoutePath . '/api.php');
        }

        // Example for loading sub-domain routes if structured that way
        // foreach (['AccessControl', 'ProcessControl', 'RiskMgt', 'AuditMgt', 'ComplianceMgt'] as $subDomain) {
        //     $subDomainRoutesPath = module_path($this->moduleName, "src/{$subDomain}/Http/routes.php");
        //     if (File::exists($subDomainRoutesPath)) {
        //         Route::prefix('api/grc/' . strtolower($subDomain))
        //             ->middleware(['api', 'auth:sanctum'])
        //             ->namespace("Modules\\GRC\\{$subDomain}\\Http\\Controllers")
        //             ->group($subDomainRoutesPath);
        //     }
        // }
    }

    public function provides()
    {
        return ['grc'];
    }
}