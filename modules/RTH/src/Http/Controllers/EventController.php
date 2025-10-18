<?php

namespace Modules\RTH\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\RTH\Domain\Entities\RthEvent;

class EventController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rth_meta' => 'required|array',
            'rth_meta.source' => 'required|string',
            'rth_meta.idempotency_key' => 'required|string',
            'type' => 'required|string',
            'payload' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $rthMeta = $request->input('rth_meta');
        $idempotencyKey = $rthMeta['idempotency_key'];

        // Check for duplicate event
        $existingEvent = RthEvent::where('idempotency_key', $idempotencyKey)->first();
        if ($existingEvent) {
            return response()->json(['rth_event_id' => $existingEvent->event_id], 202);
        }

        $eventId = (string) Str::uuid();

        RthEvent::create([
            'event_id' => $eventId,
            'source' => $rthMeta['source'],
            'type' => $request->input('type'),
            'canonical_payload' => $request->input('payload'),
            'status' => 'received',
            'idempotency_key' => $idempotencyKey,
        ]);

        return response()->json(['rth_event_id' => $eventId], 202);
    }
}