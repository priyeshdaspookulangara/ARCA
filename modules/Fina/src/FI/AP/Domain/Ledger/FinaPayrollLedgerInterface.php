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
     * Retrieves the record for a given employee from the ledger.
     *
     * @param string $employeeId
     * @return array|null
     */
    public function getEmployeeRecord(string $employeeId): ?array;
}