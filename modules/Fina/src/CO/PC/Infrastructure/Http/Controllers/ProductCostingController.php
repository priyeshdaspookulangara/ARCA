<?php

namespace Modules\Fina\CO\PC\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Fina\CO\PC\Domain\ProductCostingService;

class ProductCostingController extends Controller
{
    private ProductCostingService $productCostingService;

    public function __construct(ProductCostingService $productCostingService)
    {
        $this->productCostingService = $productCostingService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'header' => 'required|array',
            'header.product_id' => 'required|string|max:255',
            'header.costing_variant' => 'required|string|max:255',
            'header.costing_date' => 'required|date',
            'items' => 'required|array',
            'items.*.cost_element_id' => 'nullable|exists:fina_co_pc_cost_elements,id',
            'items.*.activity_type_id' => 'nullable|exists:fina_co_pc_activity_types,id',
            'items.*.quantity' => 'required|numeric',
            'items.*.rate' => 'required|numeric',
        ]);

        $productCostEstimate = $this->productCostingService->createProductCostEstimate($data['header'], $data['items']);

        return response()->json($productCostEstimate, 201);
    }

    public function show($id)
    {
        $productCostEstimate = $this->productCostingService->getProductCostEstimate($id);

        if (!$productCostEstimate) {
            return response()->json(['message' => 'Product cost estimate not found'], 404);
        }

        // Load items relationship
        $productCostEstimate->load('items');

        return response()->json($productCostEstimate);
    }

    public function index()
    {
        $productCostEstimates = $this->productCostingService->getAllProductCostEstimates();

        return response()->json($productCostEstimates);
    }

    public function destroy($id)
    {
        $deleted = $this->productCostingService->deleteProductCostEstimate($id);

        if (!$deleted) {
            return response()->json(['message' => 'Product cost estimate not found'], 404);
        }

        return response()->json(null, 204);
    }
}