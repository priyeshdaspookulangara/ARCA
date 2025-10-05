<?php

namespace Modules\Fina\FI\AP\Infrastructure\Services;

use Modules\Fina\FI\AP\Application\Services\PayrollIntegrationServiceInterface;
use Modules\Fina\FI\AP\Domain\Ledger\FinaPayrollLedgerInterface;

class PayrollIntegrationService implements PayrollIntegrationServiceInterface
{
    private $payrollLedger;

    public function __construct(FinaPayrollLedgerInterface $payrollLedger)
    {
        $this->payrollLedger = $payrollLedger;
    }

    /**
     * Updates the salary for a given employee in the financial system.
     *
     * @param string $employeeId
     * @param float $newSalary
     * @return void
     */
    public function updateEmployeeSalary(string $employeeId, float $newSalary): void
    {
        $this->payrollLedger->updateEmployeeSalary($employeeId, $newSalary);
    }

    /**
     * Updates the bank details for a given employee in the financial system.
     *
     * @param string $employeeId
     * @param string $bankDetails
     * @return void
     */
    public function updateEmployeeBankDetails(string $employeeId, string $bankDetails): void
    {
        $this->payrollLedger->updateEmployeeBankDetails($employeeId, $bankDetails);
    }

    public function updateEmployeeWorkSchedule(string $employeeId, string $workSchedule): void
    {
        $this->payrollLedger->updateEmployeeWorkSchedule($employeeId, $workSchedule);
    }

    public function updateEmployeeEmploymentType(string $employeeId, string $employmentType): void
    {
        $this->payrollLedger->updateEmployeeEmploymentType($employeeId, $employmentType);
    }

    public function updateEmployeeLeaveStatus(string $employeeId, bool $onLeave): void
    {
        $this->payrollLedger->updateEmployeeLeaveStatus($employeeId, $onLeave);
    }

    public function addApprovedWorkedHours(string $employeeId, float $hours): void
    {
        $this->payrollLedger->addWorkedHours($employeeId, $hours);
    }
}