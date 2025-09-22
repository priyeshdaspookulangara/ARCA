<?php

namespace Modules\Fina\FI\BL\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\BL\Application\BankStatementService;

class BankStatementController extends Controller
{
    private $bankStatementService;

    public function __construct(BankStatementService $bankStatementService)
    {
        $this->bankStatementService = $bankStatementService;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $bankStatement = $this->bankStatementService->createBankStatement($data);
        return response()->json($bankStatement, 201);
    }

    public function show(int $id)
    {
        $bankStatement = $this->bankStatementService->getBankStatement($id);
        if (!$bankStatement) {
            return response()->json(['message' => 'Bank statement not found'], 404);
        }
        return response()->json($bankStatement);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $result = $this->bankStatementService->updateBankStatement($id, $data);
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id)
    {
        $result = $this->bankStatementService->deleteBankStatement($id);
        return response()->json(['success' => $result]);
    }
}
