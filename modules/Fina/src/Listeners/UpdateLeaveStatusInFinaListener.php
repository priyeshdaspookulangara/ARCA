<?php

namespace Modules\Fina\Listeners;

use Modules\HR\PersonnelAdmin\Domain\Events\LongTermLeaveStartedEvent;
use Modules\HR\PersonnelAdmin\Domain\Events\LongTermLeaveEndedEvent;
use Modules\Fina\FI\AP\Application\Services\PayrollIntegrationServiceInterface;

class UpdateLeaveStatusInFinaListener
{
    private $payrollIntegrationService;

    public function __construct(PayrollIntegrationServiceInterface $payrollIntegrationService)
    {
        $this->payrollIntegrationService = $payrollIntegrationService;
    }

    public function handle($event)
    {
        if ($event instanceof LongTermLeaveStartedEvent) {
            $this->payrollIntegrationService->updateEmployeeLeaveStatus($event->employeeId, true);
        } elseif ($event instanceof LongTermLeaveEndedEvent) {
            $this->payrollIntegrationService->updateEmployeeLeaveStatus($event->employeeId, false);
        }
    }
}