<?php

namespace Modules\MM\InventoryManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Logic to move stock between locations
        return response()->json(['message' => 'Stock transferred successfully'], 201);
    }
}