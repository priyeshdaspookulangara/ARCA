<?php

namespace Modules\EventBus\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\EventBus\Models\EventAudit;
use Modules\EventBus\Models\DeadLetter;
use Modules\EventBus\Models\EventMaster;

class DeliverEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $eventId;
    protected $subscriber;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($eventId, $subscriber)
    {
        $this->eventId = $eventId;
        $this->subscriber = $subscriber;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public $tries = 3;
    public $backoff = [60, 300, 900];

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $event = EventMaster::find($this->eventId);
        if (!$event) {
            Log::error("Event not found: {$this->eventId}");
            return;
        }

        $response = Http::post($this->subscriber->endpoint_url, [
            'EventID' => $event->event_id,
            'EventType' => $event->type,
            'Source' => $event->source,
            'Timestamp' => $event->created_at,
            'Payload' => json_decode($event->payload_json, true),
        ]);

        $audit = EventAudit::where('event_id', $this->eventId)->first();
        if (!$audit) {
            $audit = new EventAudit(['event_id' => $this->eventId]);
        }

        $audit->attempts++;
        $audit->last_attempt = now();

        if ($response->successful()) {
            $audit->status = 'Delivered';
            $audit->save();
        } else {
            $audit->status = 'Failed';
            $audit->error_log = $response->body();
            $audit->save();

            if ($this->attempts() >= $this->tries) {
                DeadLetter::create([
                    'event_id' => $this->eventId,
                    'reason' => 'Failed after 3 attempts. Last error: ' . $response->body(),
                    'archived_at' => now(),
                ]);
            }

            $this->release($this->backoff[$this->attempts() - 1]);
        }
    }
}
