<?php

namespace Modules\Fina\TR\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\TR\Domain\CashPositionService;

class CashPositionController extends Controller
{
    private CashPositionService $cashPositionService;

    public function __construct(CashPositionService $cashPositionService)
    {
        $this->cashPositionService = $cashPositionService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'position_date' => 'required|date',
            'currency' => 'required|string|max:3',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $cashPosition = $this->cashPositionService->createCashPosition($data);

        return response()->json($cashPosition, 201);
    }

    public function show($id)
    {
        $cashPosition = $this->cashPositionService->getCashPosition($id);

        if (!$cashPosition) {
            return response()->json(['message' => 'Cash position not found'], 404);
        }

        return response()->json($cashPosition);
    }

    public function index()
    {
        $cashPositions = $this->cashPositionService->getAllCashPositions();

        return response()->json($cashPositions);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'position_date' => 'sometimes|required|date',
            'currency' => 'sometimes|required|string|max:3',
            'amount' => 'sometimes|required|numeric',
            'description' => 'nullable|string',
        ]);

        $cashPosition = $this->cashPositionService->updateCashPosition($id, $data);

        if (!$cashPosition) {
            return response()->json(['message' => 'Cash position not found'], 404);
        }

        return response()->json($cashPosition);
    }

    public function destroy($id)
    {
        $deleted = $this->cashPositionService->deleteCashPosition($id);

        if (!$deleted) {
            return response()->json(['message' => 'Cash position not found'], 404);
        }

        return response()->json(null, 204);
    }
}