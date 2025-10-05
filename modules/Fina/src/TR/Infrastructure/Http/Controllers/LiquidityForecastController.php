<?php

namespace Modules\Fina\TR\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\TR\Domain\LiquidityForecastService;

class LiquidityForecastController extends Controller
{
    private LiquidityForecastService $liquidityForecastService;

    public function __construct(LiquidityForecastService $liquidityForecastService)
    {
        $this->liquidityForecastService = $liquidityForecastService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'forecast_date' => 'required|date',
            'currency' => 'required|string|max:3',
            'inflows' => 'required|numeric',
            'outflows' => 'required|numeric',
            'net_flow' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $liquidityForecast = $this->liquidityForecastService->createLiquidityForecast($data);

        return response()->json($liquidityForecast, 201);
    }

    public function show($id)
    {
        $liquidityForecast = $this->liquidityForecastService->getLiquidityForecast($id);

        if (!$liquidityForecast) {
            return response()->json(['message' => 'Liquidity forecast not found'], 404);
        }

        return response()->json($liquidityForecast);
    }

    public function index()
    {
        $liquidityForecasts = $this->liquidityForecastService->getAllLiquidityForecasts();

        return response()->json($liquidityForecasts);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'forecast_date' => 'sometimes|required|date',
            'currency' => 'sometimes|required|string|max:3',
            'inflows' => 'sometimes|required|numeric',
            'outflows' => 'sometimes|required|numeric',
            'net_flow' => 'sometimes|required|numeric',
            'description' => 'nullable|string',
        ]);

        $liquidityForecast = $this->liquidityForecastService->updateLiquidityForecast($id, $data);

        if (!$liquidityForecast) {
            return response()->json(['message' => 'Liquidity forecast not found'], 404);
        }

        return response()->json($liquidityForecast);
    }

    public function destroy($id)
    {
        $deleted = $this->liquidityForecastService->deleteLiquidityForecast($id);

        if (!$deleted) {
            return response()->json(['message' => 'Liquidity forecast not found'], 404);
        }

        return response()->json(null, 204);
    }
}