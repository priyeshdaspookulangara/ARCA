<?php

namespace Modules\Fina\Listeners;

use Modules\HR\PersonnelAdmin\Domain\Events\EmployeeSalaryUpdatedEvent;
use Modules\Fina\FI\AP\Application\Services\PayrollIntegrationServiceInterface;

class UpdateEmployeeSalaryInFinaListener
{
    private $payrollIntegrationService;

    /**
     * Create the event listener.
     *
     * @param PayrollIntegrationServiceInterface $payrollIntegrationService
     */
    public function __construct(PayrollIntegrationServiceInterface $payrollIntegrationService)
    {
        $this->payrollIntegrationService = $payrollIntegrationService;
    }

    /**
     * Handle the event.
     *
     * @param  EmployeeSalaryUpdatedEvent  $event
     * @return void
     */
    public function handle(EmployeeSalaryUpdatedEvent $event)
    {
        $this->payrollIntegrationService->updateEmployeeSalary($event->employeeId, $event->newSalary);
    }
}