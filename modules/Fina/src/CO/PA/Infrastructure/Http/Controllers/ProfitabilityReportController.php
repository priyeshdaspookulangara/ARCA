<?php

namespace Modules\Fina\CO\PA\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\CO\PA\Domain\ProfitabilityReportService;

class ProfitabilityReportController extends Controller
{
    private ProfitabilityReportService $profitabilityReportService;

    public function __construct(ProfitabilityReportService $profitabilityReportService)
    {
        $this->profitabilityReportService = $profitabilityReportService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'market_segment_id' => 'required|exists:fina_co_pa_market_segments,id',
            'revenue' => 'required|numeric',
            'cost' => 'required|numeric',
            'profit' => 'required|numeric',
            'period' => 'required|date',
        ]);

        $profitabilityReport = $this->profitabilityReportService->createProfitabilityReport($data);

        return response()->json($profitabilityReport, 201);
    }

    public function show($id)
    {
        $profitabilityReport = $this->profitabilityReportService->getProfitabilityReport($id);

        if (!$profitabilityReport) {
            return response()->json(['message' => 'Profitability report not found'], 404);
        }

        return response()->json($profitabilityReport);
    }

    public function index()
    {
        $profitabilityReports = $this->profitabilityReportService->getAllProfitabilityReports();

        return response()->json($profitabilityReports);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'market_segment_id' => 'sometimes|required|exists:fina_co_pa_market_segments,id',
            'revenue' => 'sometimes|required|numeric',
            'cost' => 'sometimes|required|numeric',
            'profit' => 'sometimes|required|numeric',
            'period' => 'sometimes|required|date',
        ]);

        $profitabilityReport = $this->profitabilityReportService->updateProfitabilityReport($id, $data);

        if (!$profitabilityReport) {
            return response()->json(['message' => 'Profitability report not found'], 404);
        }

        return response()->json($profitabilityReport);
    }

    public function destroy($id)
    {
        $deleted = $this->profitabilityReportService->deleteProfitabilityReport($id);

        if (!$deleted) {
            return response()->json(['message' => 'Profitability report not found'], 404);
        }

        return response()->json(null, 204);
    }
}