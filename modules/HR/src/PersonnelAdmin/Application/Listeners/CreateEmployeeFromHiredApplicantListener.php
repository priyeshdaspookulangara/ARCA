<?php

namespace Modules\HR\PersonnelAdmin\Application\Listeners;

use Modules\HR\Recruitment\Domain\Events\ApplicantHiredEvent;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;

class CreateEmployeeFromHiredApplicantListener
{
    private $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Handle the event.
     *
     * @param  ApplicantHiredEvent  $event
     * @return void
     */
    public function handle(ApplicantHiredEvent $event)
    {
        // In a real application, you would map more data from the applicant/application
        // to the new employee record. For this demonstration, we'll create a basic employee.
        $newEmployee = new Employee($event->applicant->getId());
        $newEmployee->setLastName($event->applicant->getLastName());
        // You would likely set a default salary, position, etc. here or have another process for it.

        $this->employeeRepository->save($newEmployee);
    }
}