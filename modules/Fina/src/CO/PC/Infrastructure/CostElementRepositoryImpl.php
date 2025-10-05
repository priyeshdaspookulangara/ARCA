<?php

namespace Modules\Fina\CO\PC\Infrastructure;

use Modules\Fina\CO\PC\Domain\CostElement;
use Modules\Fina\CO\PC\Domain\Repositories\CostElementRepository;
use Illuminate\Support\Collection;

class CostElementRepositoryImpl implements CostElementRepository
{
    public function findById(int $id): ?CostElement
    {
        return CostElement::find($id);
    }

    public function getAll(): Collection
    {
        return CostElement::all();
    }

    public function save(CostElement $costElement): void
    {
        $costElement->save();
    }

    public function delete(CostElement $costElement): void
    {
        $costElement->delete();
    }
}