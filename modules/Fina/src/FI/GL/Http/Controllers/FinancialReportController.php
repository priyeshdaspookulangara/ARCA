<?php

namespace Modules\Fina\FI\GL\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\GL\Application\TrialBalanceReportService;
use Modules\Fina\FI\GL\Application\PAndLReportService;
use Modules\Fina\FI\GL\Application\BalanceSheetReportService;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    /**
     * Generate a Trial Balance report.
     * @param Request $request
     ** @param TrialBalanceReportService $reportService
     * @return JsonResponse
     */
    public function trialBalance(Request $request, TrialBalanceReportService $reportService): JsonResponse
    {
        $validated = $request->validate([
            'company_code_id' => 'required|integer|exists:fina_company_codes,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $reportData = $reportService->handle(
            $validated['company_code_id'],
            Carbon::parse($validated['from_date']),
            Carbon::parse($validated['to_date'])
        );

        return response()->json($reportData);
    }

    /**
     * Generate a Profit & Loss report.
     * @param Request $request
     * @param PAndLReportService $reportService
     * @return JsonResponse
     */
    public function profitAndLoss(Request $request, PAndLReportService $reportService): JsonResponse
    {
        $validated = $request->validate([
            'company_code_id' => 'required|integer|exists:fina_company_codes,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $reportData = $reportService->handle(
            $validated['company_code_id'],
            Carbon::parse($validated['from_date']),
            Carbon::parse($validated['to_date'])
        );

        return response()->json($reportData);
    }

    /**
     * Generate a Balance Sheet report.
     * @param Request $request
     * @param BalanceSheetReportService $reportService
     * @return JsonResponse
     */
    public function balanceSheet(Request $request, BalanceSheetReportService $reportService): JsonResponse
    {
        $validated = $request->validate([
            'company_code_id' => 'required|integer|exists:fina_company_codes,id',
            'as_of_date' => 'required|date',
        ]);

        $reportData = $reportService->handle(
            $validated['company_code_id'],
            Carbon::parse($validated['as_of_date'])
        );

        return response()->json($reportData);
    }
}
