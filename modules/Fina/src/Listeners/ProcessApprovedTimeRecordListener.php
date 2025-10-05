<?php

namespace Modules\Fina\Listeners;

use Modules\HR\TimeManagement\Domain\Events\TimeRecordApprovedEvent;
use Modules\Fina\FI\AP\Application\Services\PayrollIntegrationServiceInterface;

class ProcessApprovedTimeRecordListener
{
    private $payrollIntegrationService;

    public function __construct(PayrollIntegrationServiceInterface $payrollIntegrationService)
    {
        $this->payrollIntegrationService = $payrollIntegrationService;
    }

    /**
     * Handle the event.
     *
     * @param  TimeRecordApprovedEvent  $event
     * @return void
     */
    public function handle(TimeRecordApprovedEvent $event)
    {
        $this->payrollIntegrationService->addApprovedWorkedHours(
            $event->timeRecord->getEmployeeId(),
            $event->timeRecord->getHours()
        );
    }
}