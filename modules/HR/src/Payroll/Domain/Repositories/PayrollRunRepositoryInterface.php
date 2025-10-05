<?php

namespace Modules\HR\Payroll\Domain\Repositories;

use Modules\HR\Payroll\Domain\Entities\PayrollRun;

interface PayrollRunRepositoryInterface
{
    public function findById(string $id): ?PayrollRun;

    public function save(PayrollRun $payrollRun): void;
}