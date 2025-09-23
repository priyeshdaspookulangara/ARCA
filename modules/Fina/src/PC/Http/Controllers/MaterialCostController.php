<?php

namespace Modules\Fina\PC\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\PC\Application\MaterialCostService;

class MaterialCostController extends Controller
{
    private $materialCostService;

    public function __construct(MaterialCostService $materialCostService)
    {
        $this->materialCostService = $materialCostService;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $materialCost = $this->materialCostService->createMaterialCost($data);
        return response()->json($materialCost, 201);
    }

    public function show(int $id)
    {
        $materialCost = $this->materialCostService->getMaterialCost($id);
        if (!$materialCost) {
            return response()->json(['message' => 'Material cost not found'], 404);
        }
        return response()->json($materialCost);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $result = $this->materialCostService->updateMaterialCost($id, $data);
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id)
    {
        $result = $this->materialCostService->deleteMaterialCost($id);
        return response()->json(['success' => $result]);
    }
}
