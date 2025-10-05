<?php

namespace Modules\Fina\CO\PC\Domain;

use Illuminate\Support\Facades\DB;
use Modules\Fina\CO\PC\Domain\Repositories\ProductCostHeaderRepository;
use Modules\Fina\CO\PC\Domain\Repositories\ProductCostItemRepository;

class ProductCostingService
{
    private ProductCostHeaderRepository $productCostHeaderRepository;
    private ProductCostItemRepository $productCostItemRepository;

    public function __construct(
        ProductCostHeaderRepository $productCostHeaderRepository,
        ProductCostItemRepository $productCostItemRepository
    ) {
        $this->productCostHeaderRepository = $productCostHeaderRepository;
        $this->productCostItemRepository = $productCostItemRepository;
    }

    public function createProductCostEstimate(array $headerData, array $itemsData): ProductCostHeader
    {
        return DB::transaction(function () use ($headerData, $itemsData) {
            $header = new ProductCostHeader($headerData);
            $this->productCostHeaderRepository->save($header);

            $totalCost = 0;
            foreach ($itemsData as $itemData) {
                $item = new ProductCostItem($itemData);
                $item->product_cost_header_id = $header->id;
                $item->cost = $item->quantity * $item->rate;
                $this->productCostItemRepository->save($item);
                $totalCost += $item->cost;
            }

            $header->total_cost = $totalCost;
            $this->productCostHeaderRepository->save($header);

            return $header;
        });
    }

    public function getProductCostEstimate(int $id): ?ProductCostHeader
    {
        return $this->productCostHeaderRepository->findById($id);
    }

    public function getAllProductCostEstimates()
    {
        return $this->productCostHeaderRepository->getAll();
    }

    public function deleteProductCostEstimate(int $id): bool
    {
        $header = $this->productCostHeaderRepository->findById($id);
        if ($header) {
            $this->productCostHeaderRepository->delete($header);
            return true;
        }
        return false;
    }
}