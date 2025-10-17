<?php

namespace Modules\Fina\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Fina\Listeners\PostGoodsReceivedJournal;
use Modules\MM\Valuation\Application\Events\GoodsReceived;
use Modules\SD\Events\BillingGeneratedEvent;
use Modules\Fina\Listeners\PostSalesJournalListener;
use Modules\POS\Events\SaleCompletedEvent;
use Modules\Fina\Listeners\PostSaleAndCOGSJournalListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        GoodsReceived::class => [
            PostGoodsReceivedJournal::class,
        ],
        BillingGeneratedEvent::class => [
            PostSalesJournalListener::class,
        ],
        SaleCompletedEvent::class => [
            PostSaleAndCOGSJournalListener::class,
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