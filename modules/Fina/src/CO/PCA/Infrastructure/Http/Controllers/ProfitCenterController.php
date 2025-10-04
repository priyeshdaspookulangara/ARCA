<?php

namespace Modules\Fina\CO\PCA\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\CO\PCA\Domain\ProfitCenterService;

class ProfitCenterController extends Controller
{
    private ProfitCenterService $profitCenterService;

    public function __construct(ProfitCenterService $profitCenterService)
    {
        $this->profitCenterService = $profitCenterService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'controlling_area_id' => 'required|exists:fina_co_controlling_areas,id',
            'responsible_person' => 'nullable|string|max:255',
        ]);

        $profitCenter = $this->profitCenterService->createProfitCenter($data);

        return response()->json($profitCenter, 201);
    }

    public function show($id)
    {
        $profitCenter = $this->profitCenterService->getProfitCenter($id);

        if (!$profitCenter) {
            return response()->json(['message' => 'Profit center not found'], 404);
        }

        return response()->json($profitCenter);
    }

    public function index()
    {
        $profitCenters = $this->profitCenterService->getAllProfitCenters();

        return response()->json($profitCenters);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'controlling_area_id' => 'sometimes|required|exists:fina_co_controlling_areas,id',
            'responsible_person' => 'nullable|string|max:255',
        ]);

        $profitCenter = $this->profitCenterService->updateProfitCenter($id, $data);

        if (!$profitCenter) {
            return response()->json(['message' => 'Profit center not found'], 404);
        }

        return response()->json($profitCenter);
    }

    public function destroy($id)
    {
        $deleted = $this->profitCenterService->deleteProfitCenter($id);

        if (!$deleted) {
            return response()->json(['message' => 'Profit center not found'], 404);
        }

        return response()->json(null, 204);
    }
}