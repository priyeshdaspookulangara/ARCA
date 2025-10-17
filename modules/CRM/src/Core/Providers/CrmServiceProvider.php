<?php

namespace Modules\CRM\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CrmServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'CRM'; // Note: Case sensitive, matches directory name

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'crm';

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
        $this->app->singleton('crm', function ($app) {
            // return new CrmModuleService(); // Example of a core service for the module
            return new \stdClass(); // Placeholder
        });

        $this->app->bind(
            \Modules\CRM\CustomerMaster\Domain\CustomerRepositoryInterface::class,
            \Modules\CRM\CustomerMaster\Infrastructure\Persistence\EloquentCustomerRepository::class
        );

        $this->app->bind(
            \Modules\CRM\Sales\Domain\LeadRepositoryInterface::class,
            \Modules\CRM\Sales\Infrastructure\Persistence\EloquentLeadRepository::class
        );

        $this->app->bind(
            \Modules\CRM\Sales\Domain\OpportunityRepositoryInterface::class,
            \Modules\CRM\Sales\Infrastructure\Persistence\EloquentOpportunityRepository::class
        );

        $this->app->bind(
            \Modules\CRM\Sales\Domain\ActivityLogRepositoryInterface::class,
            \Modules\CRM\Sales\Infrastructure\Persistence\EloquentActivityLogRepository::class
        );

        $this->app->bind(
            \Modules\CRM\Sales\Domain\InteractionHistoryRepositoryInterface::class,
            \Modules\CRM\Sales\Infrastructure\Persistence\EloquentInteractionHistoryRepository::class
        );
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
    }

    /**
     * Load module routes.
     */
    protected function loadRoutes()
    {
        $modulePath = base_path('modules/' . $this->moduleName);

        if (file_exists($modulePath . '/routes/web.php')) {
            Route::middleware('web')
                ->namespace("Modules\{$this->moduleName}\Http\Controllers") // Adjust if Http controllers are deeper
                ->group($modulePath . '/routes/web.php');
        }

        if (file_exists($modulePath . '/routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->namespace("Modules\{$this->moduleName}\Http\Controllers") // Adjust if Http controllers are deeper
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
        return ['crm'];
    }
}
