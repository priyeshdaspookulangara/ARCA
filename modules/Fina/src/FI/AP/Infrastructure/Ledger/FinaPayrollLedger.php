<?php

namespace Modules\Fina\FI\AP\Infrastructure\Ledger;

use Modules\Fina\FI\AP\Domain\Ledger\FinaPayrollLedgerInterface;

class FinaPayrollLedger implements FinaPayrollLedgerInterface
{
    private $records = [];
    private $generalLedgerPostings = [];

    public function __construct()
    {
        // Pre-seed with initial data to simulate an existing financial record.
        $this->records['123'] = ['salary' => 50000, 'bank_details' => '{"account":"111","bank":"Bank A"}', 'work_schedule' => 'Full-Time', 'employment_type' => 'Permanent', 'on_leave' => false, 'worked_hours' => 0];
        $this->records['456'] = ['salary' => 75000, 'bank_details' => '{"account":"222","bank":"Bank B"}', 'work_schedule' => 'Full-Time', 'employment_type' => 'Permanent', 'on_leave' => false, 'worked_hours' => 0];
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

    public function updateEmployeeWorkSchedule(string $employeeId, string $workSchedule): void
    {
        if (!isset($this->records[$employeeId])) {
            $this->records[$employeeId] = [];
        }
        $this->records[$employeeId]['work_schedule'] = $workSchedule;
    }

    public function updateEmployeeEmploymentType(string $employeeId, string $employmentType): void
    {
        if (!isset($this->records[$employeeId])) {
            $this->records[$employeeId] = [];
        }
        $this->records[$employeeId]['employment_type'] = $employmentType;
    }

    public function updateEmployeeLeaveStatus(string $employeeId, bool $onLeave): void
    {
        if (!isset($this->records[$employeeId])) {
            $this->records[$employeeId] = [];
        }
        $this->records[$employeeId]['on_leave'] = $onLeave;
    }

    public function addWorkedHours(string $employeeId, float $hours): void
    {
        if (!isset($this->records[$employeeId])) {
            $this->records[$employeeId] = ['worked_hours' => 0];
        }
        $this->records[$employeeId]['worked_hours'] += $hours;
    }

    public function postToGeneralLedger(array $postingData): void
    {
        $this->generalLedgerPostings[] = $postingData;
    }

    public function getGeneralLedgerPostings(): array
    {
        return $this->generalLedgerPostings;
    }
}