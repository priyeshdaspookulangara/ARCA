<?php

namespace Modules\POS\Providers;

use Illuminate\Support\ServiceProvider;

class POSServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(module_path('POS', 'routes/api.php'));
    }
}