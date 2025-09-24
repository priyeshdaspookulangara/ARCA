<?php

namespace Modules\Fina\PC\Infrastructure\Persistence;

use Modules\Fina\PC\Domain\Entities\CostObjectControlling;
use Modules\Fina\PC\Domain\Repositories\CostObjectControllingRepositoryInterface;

class EloquentCostObjectControllingRepository implements CostObjectControllingRepositoryInterface
{
    public function create(array $data): CostObjectControlling
    {
        return CostObjectControlling::create($data);
    }

    public function find(int $id): ?CostObjectControlling
    {
        return CostObjectControlling::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return CostObjectControlling::find($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return CostObjectControlling::find($id)->delete();
    }
}
