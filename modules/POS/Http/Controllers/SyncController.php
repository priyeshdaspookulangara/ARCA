<?php

namespace Modules\POS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\POS\Models\OfflineTransaction;
use Modules\POS\Models\PosSyncBatch;
use Modules\POS\Models\PosSyncEvent;
use Modules\POS\Models\SyncLog;

class SyncController extends Controller
{
    public function ingest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'events' => 'required|array',
            'events.*.rth_meta.idempotency_key' => 'required|string|distinct',
            'events.*.rth_meta.trace_id' => 'required|string',
            'events.*.rth_meta.source' => 'required|string',
            'events.*.rth_meta.created_at' => 'required|date',
            'events.*.type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $events = $request->input('events');

        // Idempotency check
        $idempotencyKeys = collect($events)->pluck('rth_meta.idempotency_key');
        $existingEvents = PosSyncEvent::whereIn('idempotency_key', $idempotencyKeys)->get()->keyBy('idempotency_key');

        if ($existingEvents->isNotEmpty()) {
            foreach ($existingEvents as $key => $existingEvent) {
                // If the event was already successfully processed, return the previous result.
                if (in_array($existingEvent->status, ['published', 'acked'])) {
                    $batch = $existingEvent->batch;
                    return response()->json([
                        'message' => 'Duplicate event already processed.',
                        'sync_batch_id' => $batch->batch_id,
                        'idempotency_key' => $key,
                    ], 409);
                }
                // If the previous attempt failed or is in DLQ, it might be replayable,
                // but for this MVP, we will reject it to prevent complex states.
                // The "fix-and-replay" path will be handled by a dedicated endpoint.
                if (in_array($existingEvent->status, ['failed', 'dlq', 'pending'])) {
                     return response()->json([
                        'message' => 'Duplicate event with a pending or failed status exists.',
                        'idempotency_key' => $key,
                        'status' => $existingEvent->status,
                    ], 409);
                }
            }
        }

        $batchId = (string) Str::uuid();

        DB::beginTransaction();
        try {
            $batch = PosSyncBatch::create([
                'batch_id' => $batchId,
                'source' => $events[0]['rth_meta']['source'],
                'events_count' => count($events),
                'status' => 'ingested',
            ]);

            foreach ($events as $eventData) {
                PosSyncEvent::create([
                    'batch_id' => $batch->id,
                    'event_id' => (string) Str::uuid(),
                    'idempotency_key' => $eventData['rth_meta']['idempotency_key'],
                    'source' => $eventData['rth_meta']['source'],
                    'type' => $eventData['type'],
                    'raw_payload' => $eventData,
                    'status' => 'pending',
                    'first_received_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json(['sync_batch_id' => $batchId], 202);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to ingest events.', 'error' => $e->getMessage()], 500);
        }
    }

    public function syncOfflineBatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'BatchID' => 'required|string',
            'Transactions' => 'required|array',
            'Transactions.*.TxnID' => 'required|string',
            'Transactions.*.payload' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $batchId = $request->input('BatchID');
        $transactions = $request->input('Transactions');

        $response = Http::post(config('pos.rth_endpoint'), [
            'BatchID' => $batchId,
            'Transactions' => $transactions,
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to sync with RTH.'], 500);
        }

        $responseData = $response->json();

        $status = 'Success';
        if ($responseData['Rejected'] > 0) {
            $status = 'Partial';
        }
        if ($responseData['Rejected'] === count($transactions)) {
            $status = 'Failed';
        }

        SyncLog::create([
            'batch_id' => $batchId,
            'count' => count($transactions),
            'status' => $status,
            'last_synced' => now(),
        ]);

        foreach ($responseData['Details'] as $detail) {
            $offlineTx = OfflineTransaction::where('transaction_id', $detail['TxnID'])->first();
            if ($offlineTx) {
                $offlineTx->status = $detail['Status'] === 'Posted' ? 'Synced' : 'Error';
                $offlineTx->sync_attempts += 1;
                $offlineTx->save();
            }
        }

        return response()->json($responseData);
    }
}