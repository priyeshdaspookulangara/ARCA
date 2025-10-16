<?php

namespace Modules\SD\Providers;

use Illuminate\Support\ServiceProvider;

class SDServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(module_path('SD', 'database/migrations'));
        $this->loadRoutesFrom(module_path('SD', 'routes/api.php'));
    }
}