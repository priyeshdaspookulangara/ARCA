<?php

namespace Modules\POS\Console;

use Illuminate\Console\Command;
use Modules\POS\Models\OfflineTransaction;
use Carbon\Carbon;

class PosPurgeSyncedTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:purge-synced-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges synced offline transactions older than 48 hours.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting purge of synced offline transactions...');

        $cutoffDate = Carbon::now()->subHours(48);

        $deletedCount = OfflineTransaction::where('status', 'Synced')
            ->where('updated_at', '<', $cutoffDate)
            ->delete();

        $this->info("Purged {$deletedCount} synced offline transactions.");

        $this->info('Purge of synced offline transactions finished.');
    }
}
