<?php

namespace Modules\Payments\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Modules\Payments\Console\SettlementCommand;
use Modules\Payments\Events\PaymentInitiated;
use Modules\Payments\Events\PaymentCompleted;
use Modules\Payments\Events\PaymentFailed;
use Modules\Payments\Events\PaymentRefunded;
use Modules\Payments\Listeners\CreateJournalEntry;
use Modules\Payments\Listeners\NotifyCustomer;

class PaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            SettlementCommand::class,
        ]);
    }

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(
            PaymentCompleted::class,
            [CreateJournalEntry::class, 'handle']
        );

        Event::listen(
            PaymentCompleted::class,
            [NotifyCustomer::class, 'handle']
        );
    }
}
