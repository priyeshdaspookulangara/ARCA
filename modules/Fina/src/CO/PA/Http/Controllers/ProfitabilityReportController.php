<?php

namespace Modules\Fina\CO\PA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\CO\PA\Application\Services\ProfitabilityReportService;

class ProfitabilityReportController extends Controller
{
    protected $profitabilityReportService;

    public function __construct(ProfitabilityReportService $profitabilityReportService)
    {
        $this->profitabilityReportService = $profitabilityReportService;
    }

    public function index(Request $request)
    {
        if ($request->has('market_segment_id')) {
            return $this->profitabilityReportService->getProfitabilityReportsByMarketSegment($request->market_segment_id);
        }
        return $this->profitabilityReportService->getAllProfitabilityReports();
    }

    public function show(int $id)
    {
        return $this->profitabilityReportService->getProfitabilityReportById($id);
    }

    public function store(Request $request)
    {
        return $this->profitabilityReportService->createProfitabilityReport($request->all());
    }

    public function update(Request $request, int $id)
    {
        return $this->profitabilityReportService->updateProfitabilityReport($id, $request->all());
    }

    public function destroy(int $id)
    {
        $this->profitabilityReportService->deleteProfitabilityReport($id);
        return response()->json(null, 204);
    }
}