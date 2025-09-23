<?php

namespace Modules\Fina\PC\Domain\Repositories;

use Modules\Fina\PC\Domain\Entities\InventoryValuation;

interface InventoryValuationRepositoryInterface
{
    public function create(array $data): InventoryValuation;
    public function find(int $id): ?InventoryValuation;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
