<?php

namespace Modules\EventBus\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\EventBus\Tests\TestCase;
use Illuminate\Support\Facades\Bus;
use Modules\EventBus\Jobs\ProcessEvent;
use Modules\EventBus\Services\EventProducer;
use Modules\EventBus\Models\EventSubscription;

class EventBusTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_and_subscribe_to_event()
    {
        Bus::fake();

        EventSubscription::create([
            'topic' => 'Test.Event',
            'subscriber_module' => 'TestModule',
            'endpoint_url' => 'http://localhost/test',
        ]);

        $eventProducer = new EventProducer();
        $eventProducer->publish('Test.Event', 'TestSource', ['test' => 'data']);

        Bus::assertDispatched(ProcessEvent::class);
    }
}
