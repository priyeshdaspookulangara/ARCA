<?php

namespace Modules\ARCA_API_Gateway\Providers;

use Illuminate\Support\ServiceProvider;

class GatewayServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/gateway.php', 'gateway'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $this->publishes([
            __DIR__.'/../config/gateway.php' => config_path('gateway.php'),
        ]);
    }
}
