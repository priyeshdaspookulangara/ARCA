<?php

namespace Modules\IntegrationHub\Console;

use Illuminate\Console\Command;
use Modules\IntegrationHub\Services\SchedulerService;

class RunScheduledJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:run-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run scheduled integration jobs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(SchedulerService $schedulerService)
    {
        $schedulerService->runScheduledJobs();
        return 0;
    }
}
