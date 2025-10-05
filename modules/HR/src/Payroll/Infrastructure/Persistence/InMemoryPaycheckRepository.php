<?php

namespace Modules\HR\Payroll\Infrastructure\Persistence;

use Modules\HR\Payroll\Domain\Entities\Paycheck;
use Modules\HR\Payroll\Domain\Repositories\PaycheckRepositoryInterface;

class InMemoryPaycheckRepository implements PaycheckRepositoryInterface
{
    private $paychecks = [];

    public function findById(string $id): ?Paycheck
    {
        return $this->paychecks[$id] ?? null;
    }

    public function findByPayrollRun(string $payrollRunId): array
    {
        return array_filter($this->paychecks, function (Paycheck $paycheck) use ($payrollRunId) {
            return $paycheck->getPayrollRunId() === $payrollRunId;
        });
    }

    public function save(Paycheck $paycheck): void
    {
        $this->paychecks[$paycheck->getId()] = $paycheck;
    }
}