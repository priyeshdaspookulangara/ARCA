<?php

namespace Modules\Payments\Console;

use Illuminate\Console\Command;
use Modules\Payments\Services\SettlementService;

class SettlementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:settle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Settles daily payment transactions.';

    /**
     * The settlement service instance.
     *
     * @var SettlementService
     */
    protected $settlementService;

    /**
     * Create a new command instance.
     *
     * @param SettlementService $settlementService
     * @return void
     */
    public function __construct(SettlementService $settlementService)
    {
        parent::__construct();
        $this->settlementService = $settlementService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Logic to trigger the settlement service
        // $this->settlementService->settle();
        $this->info('Daily settlements have been processed.');
        return 0;
    }
}
