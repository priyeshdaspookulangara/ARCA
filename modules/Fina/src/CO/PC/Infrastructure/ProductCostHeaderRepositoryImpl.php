<?php

namespace Modules\Fina\CO\PC\Infrastructure;

use Modules\Fina\CO\PC\Domain\ProductCostHeader;
use Modules\Fina\CO\PC\Domain\Repositories\ProductCostHeaderRepository;
use Illuminate\Support\Collection;

class ProductCostHeaderRepositoryImpl implements ProductCostHeaderRepository
{
    public function findById(int $id): ?ProductCostHeader
    {
        return ProductCostHeader::find($id);
    }

    public function getAll(): Collection
    {
        return ProductCostHeader::all();
    }

    public function save(ProductCostHeader $productCostHeader): void
    {
        $productCostHeader->save();
    }

    public function delete(ProductCostHeader $productCostHeader): void
    {
        $productCostHeader->delete();
    }
}