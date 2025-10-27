<?php

namespace Modules\TaxEngine\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TaxEngine\Services\TaxReportService;

class TaxReportController extends Controller
{
    protected $reportService;

    public function __construct(TaxReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function summary(Request $request)
    {
        $report = $this->reportService->generateSummary($request->all());

        return response()->json($report);
    }
}
