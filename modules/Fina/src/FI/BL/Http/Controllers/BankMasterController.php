<?php

namespace Modules\Fina\FI\BL\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\FI\BL\Application\BankMasterService;

class BankMasterController extends Controller
{
    private $bankMasterService;

    public function __construct(BankMasterService $bankMasterService)
    {
        $this->bankMasterService = $bankMasterService;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $bankMaster = $this->bankMasterService->createBankMaster($data);
        return response()->json($bankMaster, 201);
    }

    public function show(int $id)
    {
        $bankMaster = $this->bankMasterService->getBankMaster($id);
        if (!$bankMaster) {
            return response()->json(['message' => 'Bank not found'], 404);
        }
        return response()->json($bankMaster);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $result = $this->bankMasterService->updateBankMaster($id, $data);
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id)
    {
        $result = $this->bankMasterService->deleteBankMaster($id);
        return response()->json(['success' => $result]);
    }
}
