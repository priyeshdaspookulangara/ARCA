<?php

namespace Modules\Fina\Listeners;

use Modules\HR\PersonnelAdmin\Domain\Events\WorkScheduleChangedEvent;
use Modules\Fina\FI\AP\Application\Services\PayrollIntegrationServiceInterface;

class UpdateWorkScheduleInFinaListener
{
    private $payrollIntegrationService;

    public function __construct(PayrollIntegrationServiceInterface $payrollIntegrationService)
    {
        $this->payrollIntegrationService = $payrollIntegrationService;
    }

    /**
     * Handle the event.
     *
     * @param  WorkScheduleChangedEvent  $event
     * @return void
     */
    public function handle(WorkScheduleChangedEvent $event)
    {
        if (isset($event->changedData['work_schedule'])) {
            $this->payrollIntegrationService->updateEmployeeWorkSchedule($event->employeeId, $event->changedData['work_schedule']);
        }

        if (isset($event->changedData['employment_type'])) {
            $this->payrollIntegrationService->updateEmployeeEmploymentType($event->employeeId, $event->changedData['employment_type']);
        }
    }
}