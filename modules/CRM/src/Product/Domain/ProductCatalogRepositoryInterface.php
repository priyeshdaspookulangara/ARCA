<?php

namespace Modules\CRM\Product\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\Product\Domain\Model\ProductCatalog;

interface ProductCatalogRepositoryInterface
{
    public function findById(int $id): ?ProductCatalog;

    public function getAll(): Collection;

    public function save(ProductCatalog $productCatalog): ProductCatalog;

    public function delete(ProductCatalog $productCatalog): bool;
}