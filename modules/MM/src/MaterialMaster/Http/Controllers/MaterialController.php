<?php

namespace Modules\MM\MaterialMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Logic to list materials
        return response()->json(['message' => 'List of materials']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Logic to create a new material
        return response()->json(['message' => 'Material created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        // Logic to show a specific material
        return response()->json(['message' => "Material details for ID: $id"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Logic to update a material
        return response()->json(['message' => "Material ID: $id updated successfully"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        // Logic to delete a material
        return response()->json(['message' => "Material ID: $id deleted successfully"]);
    }
}