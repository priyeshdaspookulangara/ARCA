<?php

namespace Modules\HR\Payroll\Domain\Repositories;

use Modules\HR\Payroll\Domain\Entities\Paycheck;

interface PaycheckRepositoryInterface
{
    public function findById(string $id): ?Paycheck;

    public function findByPayrollRun(string $payrollRunId): array;

    public function save(Paycheck $paycheck): void;
}