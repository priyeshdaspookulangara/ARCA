<?php

namespace Modules\Fina\CO\CCA\Infrastructure\Persistence;

use Modules\Fina\CO\CCA\Domain\Entities\CostCenter;
use Modules\Fina\CO\CCA\Domain\Repositories\CostCenterRepositoryInterface;

class EloquentCostCenterRepository implements CostCenterRepositoryInterface
{
    public function create(array $data): CostCenter
    {
        return CostCenter::create($data);
    }

    public function find(int $id): ?CostCenter
    {
        return CostCenter::find($id);
    }
}
