<?php

namespace Modules\Fina\CO\PA\Domain\Repositories;

use Modules\Fina\CO\PA\Domain\ProfitabilityReport;
use Illuminate\Support\Collection;

interface ProfitabilityReportRepository
{
    public function findById(int $id): ?ProfitabilityReport;

    public function getAll(): Collection;

    public function save(ProfitabilityReport $profitabilityReport): void;

    public function delete(ProfitabilityReport $profitabilityReport): void;
}