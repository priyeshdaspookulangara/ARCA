<?php

namespace Modules\Fina\CO\PC\Domain\Repositories;

use Modules\Fina\CO\PC\Domain\ProductCostItem;
use Illuminate\Support\Collection;

interface ProductCostItemRepository
{
    public function findById(int $id): ?ProductCostItem;

    public function getByHeaderId(int $headerId): Collection;

    public function save(ProductCostItem $productCostItem): void;

    public function delete(ProductCostItem $productCostItem): void;
}