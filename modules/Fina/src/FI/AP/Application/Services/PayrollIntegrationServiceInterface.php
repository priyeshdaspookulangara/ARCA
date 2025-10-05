<?php

namespace Modules\Fina\FI\AP\Application\Services;

interface PayrollIntegrationServiceInterface
{
    /**
     * Updates the salary for a given employee in the financial system.
     *
     * @param string $employeeId
     * @param float $newSalary
     * @return void
     */
    public function updateEmployeeSalary(string $employeeId, float $newSalary): void;

    /**
     * Updates the bank details for a given employee in the financial system.
     *
     * @param string $employeeId
     * @param array $bankDetails
     * @return void
     */
    public function updateEmployeeBankDetails(string $employeeId, string $bankDetails): void;

    /**
     * Updates the work schedule for a given employee in the financial system.
     *
     * @param string $employeeId
     * @param string $workSchedule
     * @return void
     */
    public function updateEmployeeWorkSchedule(string $employeeId, string $workSchedule): void;

    /**
     * Updates the employment type for a given employee in the financial system.
     *
     * @param string $employeeId
     * @param string $employmentType
     * @return void
     */
    public function updateEmployeeEmploymentType(string $employeeId, string $employmentType): void;

    /**
     * Updates the leave status for a given employee in the financial system.
     *
     * @param string $employeeId
     * @param bool $onLeave
     * @return void
     */
    public function updateEmployeeLeaveStatus(string $employeeId, bool $onLeave): void;

    /**
     * Adds approved worked hours to an employee's record for payroll calculation.
     *
     * @param string $employeeId
     * @param float $hours
     * @return void
     */
    public function addApprovedWorkedHours(string $employeeId, float $hours): void;

    /**
     * Posts the results of a completed payroll run to the financial system.
     *
     * @param array $payrollData
     * @return void
     */
    public function postPayrollRunResults(array $payrollData): void;
}