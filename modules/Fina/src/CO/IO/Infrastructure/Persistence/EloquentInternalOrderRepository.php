<?php

namespace Modules\Fina\CO\IO\Infrastructure\Persistence;

use Modules\Fina\CO\IO\Domain\Entities\InternalOrder;
use Modules\Fina\CO\IO\Domain\Repositories\InternalOrderRepositoryInterface;

class EloquentInternalOrderRepository implements InternalOrderRepositoryInterface
{
    public function create(array $data): InternalOrder
    {
        return InternalOrder::create($data);
    }

    public function find(int $id): ?InternalOrder
    {
        return InternalOrder::find($id);
    }
}
