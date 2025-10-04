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
    public function updateEmployeeBankDetails(string $employeeId, array $bankDetails): void;
}