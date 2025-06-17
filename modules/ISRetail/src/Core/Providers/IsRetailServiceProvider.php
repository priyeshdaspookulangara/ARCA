<?php

namespace Modules\ISRetail\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class IsRetailServiceProvider extends ServiceProvider
{
    protected \$moduleName = 'ISRetail'; // Using ISRetail as the directory name
    protected \$moduleNameLower = 'isretail';

    public function boot()
    {
        \$this->registerConfig();
        \$this->loadMigrationsFrom(module_path(\$this->moduleName, 'database/migrations'));
        \$this->registerTranslations();
        \$this->loadModuleRoutes();
    }

    public function register()
    {
        \$this->app->singleton('isretail', function (\$app) {
            return new \stdClass(); // Placeholder for a core ISRetail service facade
        });

        // Register MasterData services and repositories
        // e.g., \$this->app->bind(Modules\ISRetail\MasterData\Article\Domain\GenericArticleRepositoryInterface::class, Modules\ISRetail\MasterData\Article\Infrastructure\EloquentGenericArticleRepository::class);
    }

    protected function registerConfig()
    {
        \$configPath = module_path(\$this->moduleName, 'config/config.php');
        if (!File::exists(\$configPath)) {
            // Create a default config file if it doesn't exist to avoid mergeConfigFrom errors
            if (!File::isDirectory(dirname(\$configPath))) {
                File::makeDirectory(dirname(\$configPath), 0755, true, true);
            }
            File::put(\$configPath, "<?php

return [
    'name' => 'ISRetail',
];");
        }

        \$this->publishes([
            \$configPath => config_path(\$this->moduleNameLower . '.php'),
        ], 'config');
        \$this->mergeConfigFrom(
            \$configPath, \$this->moduleNameLower
        );
    }

    protected function registerTranslations()
    {
        \$langPath = module_path(\$this->moduleName, 'resources/lang');
        // Ensure lang directory exists before trying to load from it
        if (!File::isDirectory(\$langPath)) {
            File::makeDirectory(\$langPath . '/en', 0755, true, true);
            // Ensure the general.php file exists as well
            if (!File::exists(\$langPath . '/en/general.php')) {
                 File::put(\$langPath . '/en/general.php', "<?php

return ['module_name' => 'IS-Retail Solution'];");
            }
        } else if (!File::isDirectory(\$langPath . '/en')) {
             File::makeDirectory(\$langPath . '/en', 0755, true, true);
             if (!File::exists(\$langPath . '/en/general.php')) {
                 File::put(\$langPath . '/en/general.php', "<?php

return ['module_name' => 'IS-Retail Solution'];");
            }
        }


        \$this->publishes([
            \$langPath => resource_path('lang/modules/' . \$this->moduleNameLower)
        ], ['lang', \$this->moduleNameLower . '-module-translations']);

        \$this->loadTranslationsFrom(\$langPath, \$this->moduleNameLower);
    }

    protected function loadModuleRoutes()
    {
        \$routesPathApi = module_path(\$this->moduleName, 'routes/api_masterdata.php');

        // Ensure route files exist or create placeholders
        if (!File::exists(\$routesPathApi)) {
            if (!File::isDirectory(dirname(\$routesPathApi))) {
                File::makeDirectory(dirname(\$routesPathApi), 0755, true, true);
            }
            File::put(\$routesPathApi, "<?php
use Illuminate\Support\Facades\Route;

Route::get('/isretail-masterdata-status', function() { return ['status' => 'ISRetail MasterData API active']; });");
        }

        if (File::exists(\$routesPathApi)) {
            Route::prefix('api/isretail/masterdata')
                ->middleware(['api', 'auth:sanctum'])
                ->namespace("Modules\ISRetail\MasterData\Http\Controllers")
                ->group(\$routesPathApi);
        }
    }

    public function provides()
    {
        return ['isretail'];
    }
}
