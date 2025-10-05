<?php

namespace Modules\Fina\CO\PC\Domain\Repositories;

use Modules\Fina\CO\PC\Domain\CostElement;
use Illuminate\Support\Collection;

interface CostElementRepository
{
    public function findById(int $id): ?CostElement;

    public function getAll(): Collection;

    public function save(CostElement $costElement): void;

    public function delete(CostElement $costElement): void;
}