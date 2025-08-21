<?php

namespace Modules\Fina\FI\GL\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\GL\Application\BalanceCarryForwardService;

class ClosingOperationsController extends Controller
{
    /**
     * Handle the balance carry forward process for a fiscal year.
     * @param Request $request
     * @param BalanceCarryForwardService $carryForwardService
     * @return JsonResponse
     */
    public function balanceCarryForward(Request $request, BalanceCarryForwardService $carryForwardService): JsonResponse
    {
        $validated = $request->validate([
            'company_code_id' => 'required|integer|exists:fina_company_codes,id',
            'fiscal_year' => 'required|integer|digits:4',
        ]);

        try {
            $result = $carryForwardService->handle(
                $validated['company_code_id'],
                $validated['fiscal_year']
            );
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
