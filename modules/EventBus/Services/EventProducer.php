<?php

namespace Modules\EventBus\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\EventBus\Models\EventMaster;
use Modules\EventBus\Jobs\ProcessEvent;

class EventProducer
{
    public function publish(string $eventType, string $source, array $payload)
    {
        $eventId = 'EVT-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        DB::transaction(function () use ($eventId, $eventType, $source, $payload) {
            EventMaster::create([
                'event_id' => $eventId,
                'type' => $eventType,
                'source' => $source,
                'payload_json' => json_encode($payload),
            ]);

            ProcessEvent::dispatch($eventId);
        });

        return $eventId;
    }
}
