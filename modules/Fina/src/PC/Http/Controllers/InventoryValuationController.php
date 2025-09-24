<?php

namespace Modules\Fina\PC\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\PC\Application\InventoryValuationService;

class InventoryValuationController extends Controller
{
    private $inventoryValuationService;

    public function __construct(InventoryValuationService $inventoryValuationService)
    {
        $this->inventoryValuationService = $inventoryValuationService;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $inventoryValuation = $this->inventoryValuationService->createInventoryValuation($data);
        return response()->json($inventoryValuation, 201);
    }

    public function show(int $id)
    {
        $inventoryValuation = $this->inventoryValuationService->getInventoryValuation($id);
        if (!$inventoryValuation) {
            return response()->json(['message' => 'Inventory valuation not found'], 404);
        }
        return response()->json($inventoryValuation);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $result = $this->inventoryValuationService->updateInventoryValuation($id, $data);
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id)
    {
        $result = $this->inventoryValuationService->deleteInventoryValuation($id);
        return response()->json(['success' => $result]);
    }
}
