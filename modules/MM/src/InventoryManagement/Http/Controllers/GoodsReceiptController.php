<?php

namespace Modules\MM\InventoryManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Logic to record incoming goods and trigger GoodsReceived event
        return response()->json(['message' => 'Goods receipt recorded successfully'], 201);
    }
}