<?php

namespace Modules\TaxEngine\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TaxEngineServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'TaxEngine';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'taxengine';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(base_path('modules/' . $this->moduleName . '/Database/migrations'));
        $this->loadRoutesFrom(base_path('modules/' . $this->moduleName . '/Routes/api.php'));
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
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            base_path('modules/' . $this->moduleName . '/Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            base_path('modules/' . $this->moduleName . '/Config/config.php'), $this->moduleNameLower
        );
    }
}
