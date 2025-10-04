<?php

namespace Modules\Fina\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Fina\CO\PA\Domain\Repositories\MarketSegmentRepository;
use Modules\Fina\CO\PA\Infrastructure\MarketSegmentRepositoryImpl;
use Modules\Fina\CO\PA\Domain\Repositories\ProfitabilityReportRepository;
use Modules\Fina\CO\PA\Infrastructure\ProfitabilityReportRepositoryImpl;

class FinaServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // PA Bindings
        $this->app->bind(
            MarketSegmentRepository::class,
            MarketSegmentRepositoryImpl::class
        );

        $this->app->bind(
            ProfitabilityReportRepository::class,
            ProfitabilityReportRepositoryImpl::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}