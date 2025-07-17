<?php

namespace Modules\Fina\CO\CCA\Domain\Repositories;

use Modules\Fina\CO\CCA\Domain\Entities\CostCenter;

interface CostCenterRepositoryInterface
{
    public function create(array $data): CostCenter;
    public function find(int $id): ?CostCenter;
}
