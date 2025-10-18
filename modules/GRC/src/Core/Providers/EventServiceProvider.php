<?php

namespace Modules\GRC\Core\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\GRC\SharedKernel\Events\UserActionEvent;
use Modules\GRC\AuditMgt\Application\Listeners\LogUserActionEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserActionEvent::class => [
            LogUserActionEvent::class,
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