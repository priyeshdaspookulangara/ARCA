<?php

namespace Modules\Fina\CO\PA\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\CO\PA\Domain\MarketSegmentService;

class MarketSegmentController extends Controller
{
    private MarketSegmentService $marketSegmentService;

    public function __construct(MarketSegmentService $marketSegmentService)
    {
        $this->marketSegmentService = $marketSegmentService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $marketSegment = $this->marketSegmentService->createMarketSegment($data);

        return response()->json($marketSegment, 201);
    }

    public function show($id)
    {
        $marketSegment = $this->marketSegmentService->getMarketSegment($id);

        if (!$marketSegment) {
            return response()->json(['message' => 'Market segment not found'], 404);
        }

        return response()->json($marketSegment);
    }

    public function index()
    {
        $marketSegments = $this->marketSegmentService->getAllMarketSegments();

        return response()->json($marketSegments);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $marketSegment = $this->marketSegmentService->updateMarketSegment($id, $data);

        if (!$marketSegment) {
            return response()->json(['message' => 'Market segment not found'], 404);
        }

        return response()->json($marketSegment);
    }

    public function destroy($id)
    {
        $deleted = $this->marketSegmentService->deleteMarketSegment($id);

        if (!$deleted) {
            return response()->json(['message' => 'Market segment not found'], 404);
        }

        return response()->json(null, 204);
    }
}