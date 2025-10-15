<?php

namespace Modules\Fina\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Fina\Listeners\PostGoodsReceivedJournal;
use Modules\MM\Valuation\Application\Events\GoodsReceived;

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