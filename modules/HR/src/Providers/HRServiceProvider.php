<?php

namespace Modules\HR\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;
use Modules\HR\PersonnelAdmin\Infrastructure\Persistence\EmployeeRepository;
use Modules\HR\OrganizationalManagement\Domain\Repositories\OrganizationalUnitRepositoryInterface;
use Modules\HR\OrganizationalManagement\Infrastructure\Persistence\InMemoryOrganizationalUnitRepository;
use Modules\HR\OrganizationalManagement\Domain\Repositories\JobRepositoryInterface;
use Modules\HR\OrganizationalManagement\Infrastructure\Persistence\InMemoryJobRepository;
use Modules\HR\OrganizationalManagement\Domain\Repositories\PositionRepositoryInterface;
use Modules\HR\OrganizationalManagement\Infrastructure\Persistence\InMemoryPositionRepository;
use Modules\HR\TimeManagement\Domain\Repositories\TimeRecordRepositoryInterface;
use Modules\HR\TimeManagement\Infrastructure\Persistence\InMemoryTimeRecordRepository;
use Modules\HR\TimeManagement\Domain\Repositories\AbsenceRepositoryInterface;
use Modules\HR\TimeManagement\Infrastructure\Persistence\InMemoryAbsenceRepository;
use Modules\HR\Payroll\Domain\Repositories\PayrollRunRepositoryInterface;
use Modules\HR\Payroll\Infrastructure\Persistence\InMemoryPayrollRunRepository;
use Modules\HR\Payroll\Domain\Repositories\PaycheckRepositoryInterface;
use Modules\HR\Payroll\Infrastructure\Persistence\InMemoryPaycheckRepository;
use Modules\HR\Recruitment\Domain\Repositories\JobOpeningRepositoryInterface;
use Modules\HR\Recruitment\Infrastructure\Persistence\InMemoryJobOpeningRepository;
use Modules\HR\Recruitment\Domain\Repositories\ApplicantRepositoryInterface;
use Modules\HR\Recruitment\Infrastructure\Persistence\InMemoryApplicantRepository;
use Modules\HR\Recruitment\Domain\Repositories\ApplicationRepositoryInterface;
use Modules\HR\Recruitment\Infrastructure\Persistence\InMemoryApplicationRepository;
use Illuminate\Support\Facades\Event;
use Modules\HR\Recruitment\Domain\Events\ApplicantHiredEvent;
use Modules\HR\PersonnelAdmin\Application\Listeners\CreateEmployeeFromHiredApplicantListener;

class HRServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'HR';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'hr';

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

        Event::listen(
            ApplicantHiredEvent::class,
            CreateEmployeeFromHiredApplicantListener::class
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            EmployeeRepositoryInterface::class,
            EmployeeRepository::class
        );

        // OM Repositories
        $this->app->singleton(
            OrganizationalUnitRepositoryInterface::class,
            InMemoryOrganizationalUnitRepository::class
        );
        $this->app->singleton(
            JobRepositoryInterface::class,
            InMemoryJobRepository::class
        );
        $this->app->singleton(
            PositionRepositoryInterface::class,
            InMemoryPositionRepository::class
        );

        // Time Management Repositories
        $this->app->singleton(
            TimeRecordRepositoryInterface::class,
            InMemoryTimeRecordRepository::class
        );
        $this->app->singleton(
            AbsenceRepositoryInterface::class,
            InMemoryAbsenceRepository::class
        );

        // Payroll Repositories
        $this->app->singleton(
            PayrollRunRepositoryInterface::class,
            InMemoryPayrollRunRepository::class
        );
        $this->app->singleton(
            PaycheckRepositoryInterface::class,
            InMemoryPaycheckRepository::class
        );

        // Recruitment Repositories
        $this->app->singleton(
            JobOpeningRepositoryInterface::class,
            InMemoryJobOpeningRepository::class
        );
        $this->app->singleton(
            ApplicantRepositoryInterface::class,
            InMemoryApplicantRepository::class
        );
        $this->app->singleton(
            ApplicationRepositoryInterface::class,
            InMemoryApplicationRepository::class
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
        if (file_exists(module_path($this->moduleName, 'routes/web.php'))) {
            Route::middleware('web')
                ->namespace("Modules\{$this->moduleName}\Http\Controllers")
                ->group(module_path($this->moduleName, 'routes/web.php'));
        }

        if (file_exists(module_path($this->moduleName, 'routes/api.php'))) {
            Route::prefix('api')
                ->middleware('api')
                ->namespace("Modules\{$this->moduleName}\Http\Controllers")
                ->group(module_path($this->moduleName, 'routes/api.php'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
