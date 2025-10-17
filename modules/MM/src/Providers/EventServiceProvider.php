<?php

namespace Modules\MM\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\SD\Events\SalesOrderCreatedEvent;
use Modules\MM\InventoryManagement\Application\Listeners\ReserveStockListener;
use Modules\SD\Events\DeliveryCompletedEvent;
use Modules\MM\InventoryManagement\Application\Listeners\TriggerGoodsIssueListener;
use Modules\POS\Events\SaleCompletedEvent;
use Modules\MM\InventoryManagement\Application\Listeners\DeductStockListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        SalesOrderCreatedEvent::class => [
            ReserveStockListener::class,
        ],
        DeliveryCompletedEvent::class => [
            TriggerGoodsIssueListener::class,
        ],
        SaleCompletedEvent::class => [
            DeductStockListener::class,
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