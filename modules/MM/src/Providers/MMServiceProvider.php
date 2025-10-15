<?php

namespace Modules\MM\Providers;

use Illuminate\Support\ServiceProvider;

class MMServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function register()
    {
        $this->app->singleton('mm', function ($app) {
            return new \stdClass(); // Placeholder
        });
    }
}