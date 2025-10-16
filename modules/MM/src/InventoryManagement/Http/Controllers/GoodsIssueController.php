<?php

namespace Modules\MM\InventoryManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoodsIssueController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Logic to record outgoing goods and trigger GoodsIssued event
        return response()->json(['message' => 'Goods issue recorded successfully'], 201);
    }
}