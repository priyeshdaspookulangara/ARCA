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
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }

    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->singleton('mm', function ($app) {
            return new \stdClass(); // Placeholder
        });
    }
}