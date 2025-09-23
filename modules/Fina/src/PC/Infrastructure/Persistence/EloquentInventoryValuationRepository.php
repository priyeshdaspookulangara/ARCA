<?php

namespace Modules\Fina\PC\Infrastructure\Persistence;

use Modules\Fina\PC\Domain\Entities\InventoryValuation;
use Modules\Fina\PC\Domain\Repositories\InventoryValuationRepositoryInterface;

class EloquentInventoryValuationRepository implements InventoryValuationRepositoryInterface
{
    public function create(array $data): InventoryValuation
    {
        return InventoryValuation::create($data);
    }

    public function find(int $id): ?InventoryValuation
    {
        return InventoryValuation::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return InventoryValuation::find($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return InventoryValuation::find($id)->delete();
    }
}
