<?php

namespace Modules\Fina\CO\PC\Domain\Repositories;

use Modules\Fina\CO\PC\Domain\ProductCostHeader;
use Illuminate\Support\Collection;

interface ProductCostHeaderRepository
{
    public function findById(int $id): ?ProductCostHeader;

    public function getAll(): Collection;

    public function save(ProductCostHeader $productCostHeader): void;

    public function delete(ProductCostHeader $productCostHeader): void;
}