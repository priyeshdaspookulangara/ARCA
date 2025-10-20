<?php

namespace Modules\POS\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Modules\POS\Models\OfflineTransaction;

class PosSyncOfflineTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:sync-offline-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs pending offline transactions to the central server.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting offline transaction sync...');

        $pendingTransactions = OfflineTransaction::where('status', 'PendingSync')->get();

        if ($pendingTransactions->isEmpty()) {
            $this->info('No pending transactions to sync.');
            return;
        }

        $batches = $pendingTransactions->chunk(50);

        foreach ($batches as $batch) {
            $this->info("Processing batch of {$batch->count()} transactions.");

            $transactionsPayload = $batch->map(function ($transaction) {
                return [
                    'TxnID' => $transaction->transaction_id,
                    'payload' => $transaction->payload_json,
                ];
            })->toArray();

            $response = Http::post(url('/api/pos-sync/v1/sync/offline-batch'), [
                'BatchID' => 'SYNC-' . now()->format('YmdHis') . '-STORE15-B' . rand(1, 1000),
                'Transactions' => $transactionsPayload,
            ]);

            if ($response->successful()) {
                $this->info('Batch synced successfully.');
                $responseData = $response->json();
                foreach ($responseData['Details'] as $detail) {
                    if ($detail['Status'] === 'Posted') {
                        OfflineTransaction::where('transaction_id', $detail['TxnID'])->update(['status' => 'Synced']);
                    }
                }
            } else {
                $this->error('Failed to sync batch.');
                $this->error($response->body());
            }
        }

        $this->info('Offline transaction sync finished.');
    }
}
