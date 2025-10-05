<?php

namespace Modules\Fina\CO\PC\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\CO\PC\Domain\CostElementService;

class CostElementController extends Controller
{
    private CostElementService $costElementService;

    public function __construct(CostElementService $costElementService)
    {
        $this->costElementService = $costElementService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $costElement = $this->costElementService->createCostElement($data);

        return response()->json($costElement, 201);
    }

    public function show($id)
    {
        $costElement = $this->costElementService->getCostElement($id);

        if (!$costElement) {
            return response()->json(['message' => 'Cost element not found'], 404);
        }

        return response()->json($costElement);
    }

    public function index()
    {
        $costElements = $this->costElementService->getAllCostElements();

        return response()->json($costElements);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $costElement = $this->costElementService->updateCostElement($id, $data);

        if (!$costElement) {
            return response()->json(['message' => 'Cost element not found'], 404);
        }

        return response()->json($costElement);
    }

    public function destroy($id)
    {
        $deleted = $this->costElementService->deleteCostElement($id);

        if (!$deleted) {
            return response()->json(['message' => 'Cost element not found'], 404);
        }

        return response()->json(null, 204);
    }
}