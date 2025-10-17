<?php

namespace Modules\CRM\Product\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\CRM\Product\Domain\ProductCatalogRepositoryInterface;
use Modules\CRM\Product\Domain\Model\ProductCatalog;

class EloquentProductCatalogRepository implements ProductCatalogRepositoryInterface
{
    public function findById(int $id): ?ProductCatalog
    {
        return ProductCatalog::find($id);
    }

    public function getAll(): Collection
    {
        return ProductCatalog::all();
    }

    public function save(ProductCatalog $productCatalog): ProductCatalog
    {
        $productCatalog->save();
        return $productCatalog;
    }

    public function delete(ProductCatalog $productCatalog): bool
    {
        return $productCatalog->delete();
    }
}