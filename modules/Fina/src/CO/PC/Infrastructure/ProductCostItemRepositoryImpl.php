<?php

namespace Modules\Fina\CO\PC\Infrastructure;

use Modules\Fina\CO\PC\Domain\ProductCostItem;
use Modules\Fina\CO\PC\Domain\Repositories\ProductCostItemRepository;
use Illuminate\Support\Collection;

class ProductCostItemRepositoryImpl implements ProductCostItemRepository
{
    public function findById(int $id): ?ProductCostItem
    {
        return ProductCostItem::find($id);
    }

    public function getByHeaderId(int $headerId): Collection
    {
        return ProductCostItem::where('product_cost_header_id', $headerId)->get();
    }

    public function save(ProductCostItem $productCostItem): void
    {
        $productCostItem->save();
    }

    public function delete(ProductCostItem $productCostItem): void
    {
        $productCostItem->delete();
    }
}