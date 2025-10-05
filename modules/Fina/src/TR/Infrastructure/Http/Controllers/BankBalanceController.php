<?php

namespace Modules\Fina\TR\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\TR\Domain\BankBalanceService;

class BankBalanceController extends Controller
{
    private BankBalanceService $bankBalanceService;

    public function __construct(BankBalanceService $bankBalanceService)
    {
        $this->bankBalanceService = $bankBalanceService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bank_account_id' => 'required|exists:fina_bl_bank_accounts,id',
            'balance_date' => 'required|date',
            'balance' => 'required|numeric',
        ]);

        $bankBalance = $this->bankBalanceService->createBankBalance($data);

        return response()->json($bankBalance, 201);
    }

    public function show($id)
    {
        $bankBalance = $this->bankBalanceService->getBankBalance($id);

        if (!$bankBalance) {
            return response()->json(['message' => 'Bank balance not found'], 404);
        }

        return response()->json($bankBalance);
    }

    public function index()
    {
        $bankBalances = $this->bankBalanceService->getAllBankBalances();

        return response()->json($bankBalances);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'bank_account_id' => 'sometimes|required|exists:fina_bl_bank_accounts,id',
            'balance_date' => 'sometimes|required|date',
            'balance' => 'sometimes|required|numeric',
        ]);

        $bankBalance = $this->bankBalanceService->updateBankBalance($id, $data);

        if (!$bankBalance) {
            return response()->json(['message' => 'Bank balance not found'], 404);
        }

        return response()->json($bankBalance);
    }

    public function destroy($id)
    {
        $deleted = $this->bankBalanceService->deleteBankBalance($id);

        if (!$deleted) {
            return response()->json(['message' => 'Bank balance not found'], 404);
        }

        return response()->json(null, 204);
    }
}