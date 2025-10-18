<?php

namespace Modules\Analytics\Core\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Analytics\SharedKernel\Events\SaleCompletedEvent;
use Modules\Analytics\Facts\Application\Listeners\IngestSaleData;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SaleCompletedEvent::class => [
            IngestSaleData::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}