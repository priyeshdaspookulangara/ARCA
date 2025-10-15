<?php

namespace Modules\Fina\CO\PA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\CO\PA\Application\Services\MarketSegmentService;

class MarketSegmentController extends Controller
{
    protected $marketSegmentService;

    public function __construct(MarketSegmentService $marketSegmentService)
    {
        $this->marketSegmentService = $marketSegmentService;
    }

    public function index()
    {
        return $this->marketSegmentService->getAllMarketSegments();
    }

    public function show(int $id)
    {
        return $this->marketSegmentService->getMarketSegmentById($id);
    }

    public function store(Request $request)
    {
        return $this->marketSegmentService->createMarketSegment($request->all());
    }

    public function update(Request $request, int $id)
    {
        return $this->marketSegmentService->updateMarketSegment($id, $request->all());
    }

    public function destroy(int $id)
    {
        $this->marketSegmentService->deleteMarketSegment($id);
        return response()->json(null, 204);
    }
}