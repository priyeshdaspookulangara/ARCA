<?php

namespace Modules\Fina\PC\Domain\Repositories;

use Modules\Fina\PC\Domain\Entities\CostObjectControlling;

interface CostObjectControllingRepositoryInterface
{
    public function create(array $data): CostObjectControlling;
    public function find(int $id): ?CostObjectControlling;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
