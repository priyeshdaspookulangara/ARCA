<?php

namespace Modules\Fina\FI\AP\Infrastructure\Ledger;

use Modules\Fina\FI\AP\Domain\Ledger\FinaPayrollLedgerInterface;

class FinaPayrollLedger implements FinaPayrollLedgerInterface
{
    private $records = [];

    public function __construct()
    {
        // Pre-seed with initial data to simulate an existing financial record.
        $this->records['123'] = ['salary' => 50000, 'bank_details' => '{"account":"111","bank":"Bank A"}'];
        $this->records['456'] = ['salary' => 75000, 'bank_details' => '{"account":"222","bank":"Bank B"}'];
    }

    public function updateEmployeeSalary(string $employeeId, float $newSalary): void
    {
        if (!isset($this->records[$employeeId])) {
            $this->records[$employeeId] = [];
        }
        $this->records[$employeeId]['salary'] = $newSalary;
    }

    public function updateEmployeeBankDetails(string $employeeId, string $bankDetails): void
    {
        if (!isset($this->records[$employeeId])) {
            $this->records[$employeeId] = [];
        }
        $this->records[$employeeId]['bank_details'] = $bankDetails;
    }

    public function getEmployeeRecord(string $employeeId): ?array
    {
        return $this->records[$employeeId] ?? null;
    }
}