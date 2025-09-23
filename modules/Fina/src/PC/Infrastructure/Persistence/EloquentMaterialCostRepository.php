<?php

namespace Modules\Fina\PC\Infrastructure\Persistence;

use Modules\Fina\PC\Domain\Entities\MaterialCost;
use Modules\Fina\PC\Domain\Repositories\MaterialCostRepositoryInterface;

class EloquentMaterialCostRepository implements MaterialCostRepositoryInterface
{
    public function create(array $data): MaterialCost
    {
        return MaterialCost::create($data);
    }

    public function find(int $id): ?MaterialCost
    {
        return MaterialCost::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return MaterialCost::find($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return MaterialCost::find($id)->delete();
    }
}
