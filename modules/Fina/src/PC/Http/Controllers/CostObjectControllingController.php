<?php

namespace Modules\Fina\PC\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\PC\Application\CostObjectControllingService;

class CostObjectControllingController extends Controller
{
    private $costObjectControllingService;

    public function __construct(CostObjectControllingService $costObjectControllingService)
    {
        $this->costObjectControllingService = $costObjectControllingService;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $costObjectControlling = $this->costObjectControllingService->createCostObjectControlling($data);
        return response()->json($costObjectControlling, 201);
    }

    public function show(int $id)
    {
        $costObjectControlling = $this->costObjectControllingService->getCostObjectControlling($id);
        if (!$costObjectControlling) {
            return response()->json(['message' => 'Cost object controlling not found'], 404);
        }
        return response()->json($costObjectControlling);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $result = $this->costObjectControllingService->updateCostObjectControlling($id, $data);
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id)
    {
        $result = $this->costObjectControllingService->deleteCostObjectControlling($id);
        return response()->json(['success' => $result]);
    }
}
