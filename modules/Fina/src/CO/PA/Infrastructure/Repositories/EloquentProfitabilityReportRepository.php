<?php

namespace Modules\Fina\CO\PA\Infrastructure\Repositories;

use Modules\Fina\CO\PA\Domain\Entities\ProfitabilityReport;
use Modules\Fina\CO\PA\Domain\Repositories\ProfitabilityReportRepository;

class EloquentProfitabilityReportRepository implements ProfitabilityReportRepository
{
    public function getAll()
    {
        return ProfitabilityReport::all();
    }

    public function findById(int $id)
    {
        return ProfitabilityReport::find($id);
    }

    public function create(array $data)
    {
        return ProfitabilityReport::create($data);
    }

    public function update(int $id, array $data)
    {
        $profitability_report = ProfitabilityReport::findOrFail($id);
        $profitability_report->update($data);
        return $profitability_report;
    }

    public function delete(int $id)
    {
        $profitability_report = ProfitabilityReport::findOrFail($id);
        $profitability_report->delete();
    }

    public function findByMarketSegment(int $marketSegmentId)
    {
        return ProfitabilityReport::where('market_segment_id', $marketSegmentId)->get();
    }
}