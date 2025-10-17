<?php

namespace Modules\CRM\Core\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\CRM\CustomerMaster\Domain\Events\CustomerCreated;
use Modules\CRM\CustomerMaster\Application\Listeners\SyncCustomerToOtherModules;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CustomerCreated::class => [
            SyncCustomerToOtherModules::class,
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