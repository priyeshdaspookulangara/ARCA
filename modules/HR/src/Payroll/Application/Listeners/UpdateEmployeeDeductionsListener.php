<?php

namespace Modules\HR\Payroll\Application\Listeners;

use Modules\HR\Benefits\Domain\Events\BenefitEnrollmentChangedEvent;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;

class UpdateEmployeeDeductionsListener
{
    private $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Handle the event.
     *
     * @param  BenefitEnrollmentChangedEvent  $event
     * @return void
     */
    public function handle(BenefitEnrollmentChangedEvent $event)
    {
        $employee = $this->employeeRepository->findById($event->employeeId);

        if ($employee) {
            if ($event->status === 'active') {
                $employee->addRecurringDeduction($event->deductionAmount);
            } else {
                $employee->removeRecurringDeduction($event->deductionAmount);
            }
            $this->employeeRepository->save($employee);
        }
    }
}