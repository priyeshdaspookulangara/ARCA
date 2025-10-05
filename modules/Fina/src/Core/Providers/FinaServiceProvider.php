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
use Modules\Fina\TR\Domain\Repositories\CashPositionRepository;
use Modules\Fina\TR\Domain\Repositories\BankBalanceRepository;
use Modules\Fina\TR\Domain\Repositories\LiquidityForecastRepository;
use Modules\Fina\TR\Infrastructure\CashPositionRepositoryImpl;
use Modules\Fina\TR\Infrastructure\BankBalanceRepositoryImpl;
use Modules\Fina\TR\Infrastructure\LiquidityForecastRepositoryImpl;
use Modules\Fina\TR\Domain\CashPositionService;
use Modules\Fina\TR\Domain\BankBalanceService;
use Modules\Fina\TR\Domain\LiquidityForecastService;

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

        // TR Repository Bindings
        $this->app->bind(
            CashPositionRepository::class,
            CashPositionRepositoryImpl::class
        );

        $this->app->bind(
            BankBalanceRepository::class,
            BankBalanceRepositoryImpl::class
        );

        $this->app->bind(
            LiquidityForecastRepository::class,
            LiquidityForecastRepositoryImpl::class
        );

        // TR Service Bindings
        $this->app->singleton(CashPositionService::class, function ($app) {
            return new CashPositionService($app->make(CashPositionRepository::class));
        });

        $this->app->singleton(BankBalanceService::class, function ($app) {
            return new BankBalanceService($app->make(BankBalanceRepository::class));
        });

        $this->app->singleton(LiquidityForecastService::class, function ($app) {
            return new LiquidityForecastService($app->make(LiquidityForecastRepository::class));
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