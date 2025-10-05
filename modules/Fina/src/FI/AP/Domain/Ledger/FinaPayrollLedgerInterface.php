<?php

namespace Modules\Fina\FI\AP\Domain\Ledger;

interface FinaPayrollLedgerInterface
{
    /**
     * Updates the salary for a given employee in the ledger.
     *
     * @param string $employeeId
     * @param float $newSalary
     * @return void
     */
    public function updateEmployeeSalary(string $employeeId, float $newSalary): void;

    /**
     * Updates the bank details for a given employee in the ledger.
     *
     * @param string $employeeId
     * @param string $bankDetails
     * @return void
     */
    public function updateEmployeeBankDetails(string $employeeId, string $bankDetails): void;

    /**
     * Updates the work schedule for a given employee in the ledger.
     *
     * @param string $employeeId
     * @param string $workSchedule
     * @return void
     */
    public function updateEmployeeWorkSchedule(string $employeeId, string $workSchedule): void;

    /**
     * Updates the employment type for a given employee in the ledger.
     *
     * @param string $employeeId
     * @param string $employmentType
     * @return void
     */
    public function updateEmployeeEmploymentType(string $employeeId, string $employmentType): void;

    /**
     * Updates the leave status for a given employee in the ledger.
     *
     * @param string $employeeId
     * @param bool $onLeave
     * @return void
     */
    public function updateEmployeeLeaveStatus(string $employeeId, bool $onLeave): void;

    /**
     * Retrieves the record for a given employee from the ledger.
     *
     * @param string $employeeId
     * @return array|null
     */
    public function getEmployeeRecord(string $employeeId): ?array;
}