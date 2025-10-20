<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $app->register(\Modules\POS\Providers\POSServiceProvider::class);

        $this->loadMigrations($app);
        $this->loadRoutes($app);

        return $app;
    }

    protected function loadMigrations($app)
    {
        $this->artisan('migrate', ['--path' => 'modules/POS/database/migrations']);
    }

    protected function loadRoutes($app)
    {
        $routesPath = module_path('POS', 'routes/api.php');
        if (file_exists($routesPath)) {
            require $routesPath;
        }
    }
}