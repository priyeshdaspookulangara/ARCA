<?php

namespace Modules\Fina\FI\BL\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\BL\Application\BankAccountService;

class BankAccountController extends Controller
{
    private $bankAccountService;

    public function __construct(BankAccountService $bankAccountService)
    {
        $this->bankAccountService = $bankAccountService;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $bankAccount = $this->bankAccountService->createBankAccount($data);
        return response()->json($bankAccount, 201);
    }

    public function show(int $id)
    {
        $bankAccount = $this->bankAccountService->getBankAccount($id);
        if (!$bankAccount) {
            return response()->json(['message' => 'Bank account not found'], 404);
        }
        return response()->json($bankAccount);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $result = $this->bankAccountService->updateBankAccount($id, $data);
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id)
    {
        $result = $this->bankAccountService->deleteBankAccount($id);
        return response()->json(['success' => $result]);
    }
}
