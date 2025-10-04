<?php

namespace Modules\Fina\Listeners;

use Modules\HR\PersonnelAdmin\Domain\Events\EmployeePersonalDataUpdatedEvent;
use Modules\Fina\FI\AP\Application\Services\PayrollIntegrationServiceInterface;

class UpdateEmployeePersonalDataInFinaListener
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
     * @param  EmployeePersonalDataUpdatedEvent  $event
     * @return void
     */
    public function handle(EmployeePersonalDataUpdatedEvent $event)
    {
        if (isset($event->updatedData['bank_details'])) {
            $bankDetails = $event->updatedData['bank_details'];
            // Ensure bank details are in string format for the service.
            if (is_array($bankDetails)) {
                $bankDetails = json_encode($bankDetails);
            }
            $this->payrollIntegrationService->updateEmployeeBankDetails($event->employeeId, $bankDetails);
        }
    }
}