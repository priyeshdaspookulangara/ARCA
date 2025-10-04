<?php

namespace Modules\Fina\CO\PCA\Domain\Repositories;

use Modules\Fina\CO\PCA\Domain\ProfitCenter;
use Illuminate\Support\Collection;

interface ProfitCenterRepository
{
    public function findById(int $id): ?ProfitCenter;

    public function getAll(): Collection;

    public function save(ProfitCenter $profitCenter): void;

    public function delete(ProfitCenter $profitCenter): void;
}