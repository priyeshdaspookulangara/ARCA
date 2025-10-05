<?php

namespace Modules\HR\Payroll\Domain\Entities;

class Paycheck implements \JsonSerializable
{
    private $id;
    private $payrollRunId;
    private $employeeId;
    private $grossPay;
    private $deductions;
    private $netPay;

    public function __construct(string $id, string $payrollRunId, string $employeeId, float $grossPay, float $deductions)
    {
        $this->id = $id;
        $this->payrollRunId = $payrollRunId;
        $this->employeeId = $employeeId;
        $this->grossPay = $grossPay;
        $this->deductions = $deductions;
        $this->netPay = $grossPay - $deductions;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPayrollRunId(): string
    {
        return $this->payrollRunId;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function getGrossPay(): float
    {
        return $this->grossPay;
    }

    public function getDeductions(): float
    {
        return $this->deductions;
    }

    public function getNetPay(): float
    {
        return $this->netPay;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'payroll_run_id' => $this->payrollRunId,
            'employee_id' => $this->employeeId,
            'gross_pay' => $this->grossPay,
            'deductions' => $this->deductions,
            'net_pay' => $this->netPay,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}