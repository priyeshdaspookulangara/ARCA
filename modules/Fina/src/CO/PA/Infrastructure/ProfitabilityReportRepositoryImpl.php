<?php

namespace Modules\Fina\CO\PA\Infrastructure;

use Modules\Fina\CO\PA\Domain\ProfitabilityReport;
use Modules\Fina\CO\PA\Domain\Repositories\ProfitabilityReportRepository;
use Illuminate\Support\Collection;

class ProfitabilityReportRepositoryImpl implements ProfitabilityReportRepository
{
    public function findById(int $id): ?ProfitabilityReport
    {
        return ProfitabilityReport::find($id);
    }

    public function getAll(): Collection
    {
        return ProfitabilityReport::all();
    }

    public function save(ProfitabilityReport $profitabilityReport): void
    {
        $profitabilityReport->save();
    }

    public function delete(ProfitabilityReport $profitabilityReport): void
    {
        $profitabilityReport->delete();
    }
}