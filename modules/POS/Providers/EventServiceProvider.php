<?php

namespace Modules\POS\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\POS\Events\OfflineTransactionStored;
use Modules\POS\Listeners\LogOfflineTransaction;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OfflineTransactionStored::class => [
            LogOfflineTransaction::class,
        ],
    ];
}
