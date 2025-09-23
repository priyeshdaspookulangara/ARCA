<?php

namespace Modules\Fina\PC\Domain\Repositories;

use Modules\Fina\PC\Domain\Entities\MaterialCost;

interface MaterialCostRepositoryInterface
{
    public function create(array $data): MaterialCost;
    public function find(int $id): ?MaterialCost;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
