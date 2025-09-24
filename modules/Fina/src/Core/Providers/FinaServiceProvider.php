<?php

namespace Modules\Fina\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Fina\FI\BL\Domain\Repositories\BankMasterRepositoryInterface;
use Modules\Fina\FI\BL\Infrastructure\Persistence\EloquentBankMasterRepository;
use Modules\Fina\FI\BL\Domain\Repositories\BankAccountRepositoryInterface;
use Modules\Fina\FI\BL\Infrastructure\Persistence\EloquentBankAccountRepository;
use Modules\Fina\FI\BL\Domain\Repositories\BankStatementRepositoryInterface;
use Modules\Fina\FI\BL\Infrastructure\Persistence\EloquentBankStatementRepository;
use Modules\Fina\FI\BL\Application\BankMasterService;
use Modules\Fina\FI\BL\Application\BankAccountService;
use Modules\Fina\FI\BL\Application\BankStatementService;
use Modules\Fina\PC\Domain\Repositories\MaterialCostRepositoryInterface;
use Modules\Fina\PC\Infrastructure\Persistence\EloquentMaterialCostRepository;
use Modules\Fina\PC\Domain\Repositories\InventoryValuationRepositoryInterface;
use Modules\Fina\PC\Infrastructure\Persistence\EloquentInventoryValuationRepository;
use Modules\Fina\PC\Domain\Repositories\CostObjectControllingRepositoryInterface;
use Modules\Fina\PC\Infrastructure\Persistence\EloquentCostObjectControllingRepository;
use Modules\Fina\PC\Application\MaterialCostService;
use Modules\Fina\PC\Application\InventoryValuationService;
use Modules\Fina\PC\Application\CostObjectControllingService;

class FinaServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Fina';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'fina';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        $this->loadRoutes();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BankMasterRepositoryInterface::class, EloquentBankMasterRepository::class);
        $this->app->bind(BankAccountRepositoryInterface::class, EloquentBankAccountRepository::class);
        $this->app->bind(BankStatementRepositoryInterface::class, EloquentBankStatementRepository::class);

        $this->app->singleton(BankMasterService::class, function ($app) {
            return new BankMasterService($app->make(BankMasterRepositoryInterface::class));
        });

        $this->app->singleton(BankAccountService::class, function ($app) {
            return new BankAccountService($app->make(BankAccountRepositoryInterface::class));
        });

        $this->app->singleton(BankStatementService::class, function ($app) {
            return new BankStatementService($app->make(BankStatementRepositoryInterface::class));
        });

        $this->app->bind(MaterialCostRepositoryInterface::class, EloquentMaterialCostRepository::class);
        $this->app->bind(InventoryValuationRepositoryInterface::class, EloquentInventoryValuationRepository::class);
        $this->app->bind(CostObjectControllingRepositoryInterface::class, EloquentCostObjectControllingRepository::class);

        $this->app->singleton(MaterialCostService::class, function ($app) {
            return new MaterialCostService($app->make(MaterialCostRepositoryInterface::class));
        });

        $this->app->singleton(InventoryValuationService::class, function ($app) {
            return new InventoryValuationService($app->make(InventoryValuationRepositoryInterface::class));
        });

        $this->app->singleton(CostObjectControllingService::class, function ($app) {
            return new CostObjectControllingService($app->make(CostObjectControllingRepositoryInterface::class));
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/' . $this->moduleNameLower;
        }, \Config::get('view.paths')), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        $this->publishes([
            module_path($this->moduleName, 'resources/lang') => $langPath
        ], ['lang', $this->moduleNameLower . '-module-translations']);

        $this->loadTranslationsFrom(module_path($this->moduleName, 'resources/lang'), $this->moduleNameLower);
        // Fallback for lang files if not published
        // $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'resources/lang'));
    }

    /**
     * Load module routes.
     */
    protected function loadRoutes()
    {
        // Helper function for module_path (assuming it's available globally or via a trait)
        // In a real scenario, ensure module_path() is accessible.
        // For this subtask, we'll write it out, but normally it'd be cleaner.
        $modulePath = base_path('modules/' . $this->moduleName);

        if (file_exists($modulePath . '/routes/web.php')) {
            Route::middleware('web')
                ->namespace("Modules\{$this->moduleName}\Http\Controllers") // Adjust namespace if needed
                ->group($modulePath . '/routes/web.php');
        }

        if (file_exists($modulePath . '/routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->namespace("Modules\{$this->moduleName}\Http\Controllers") // Adjust namespace if needed
                ->group($modulePath . '/routes/api.php');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['fina'];
    }
}
