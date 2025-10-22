<?php

namespace Modules\EventBus\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\EventBus\Models\EventSubscription;

class EventSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EventSubscription::create([
            'topic' => 'POS.Sale.Completed',
            'subscriber_module' => 'FINA',
            'endpoint_url' => url('/api/fina/events'),
        ]);
    }
}
