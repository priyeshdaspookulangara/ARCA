<?php

namespace Modules\RTH\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RTHServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'RTH';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'rth';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
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
        //
    }

    /**
     * Load module routes.
     */
    protected function loadRoutes()
    {
        if (file_exists(module_path($this->moduleName, 'routes/api.php'))) {
            Route::prefix('api')
                ->middleware('api')
                ->namespace("Modules\\{$this->moduleName}\\Http\\Controllers")
                ->group(module_path($this->moduleName, 'routes/api.php'));
        }
    }
}