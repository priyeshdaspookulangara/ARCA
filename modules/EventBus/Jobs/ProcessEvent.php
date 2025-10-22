<?php

namespace Modules\EventBus\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\EventBus\Models\EventMaster;
use Modules\EventBus\Models\EventSubscription;
use Modules\EventBus\Models\EventAudit;

class ProcessEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $eventId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Processing event: {$this->eventId}");

        $event = EventMaster::find($this->eventId);
        if (!$event) {
            Log::error("Event not found: {$this->eventId}");
            return;
        }

        EventAudit::create([
            'event_id' => $this->eventId,
            'status' => 'Processing',
        ]);

        $subscribers = EventSubscription::where('topic', $event->type)->get();

        foreach ($subscribers as $subscriber) {
            DeliverEvent::dispatch($this->eventId, $subscriber);
        }
    }
}
