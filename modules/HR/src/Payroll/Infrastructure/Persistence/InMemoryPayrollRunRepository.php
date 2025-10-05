<?php

namespace Modules\HR\Payroll\Infrastructure\Persistence;

use Modules\HR\Payroll\Domain\Entities\PayrollRun;
use Modules\HR\Payroll\Domain\Repositories\PayrollRunRepositoryInterface;

class InMemoryPayrollRunRepository implements PayrollRunRepositoryInterface
{
    private $payrollRuns = [];

    public function findById(string $id): ?PayrollRun
    {
        return $this->payrollRuns[$id] ?? null;
    }

    public function save(PayrollRun $payrollRun): void
    {
        $this->payrollRuns[$payrollRun->getId()] = $payrollRun;
    }
}