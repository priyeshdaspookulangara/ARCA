<?php

namespace Modules\CRM\SalesForceAutomation\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\CRM\SalesForceAutomation\Domain\TerritoryRepositoryInterface;
use Modules\CRM\SalesForceAutomation\Domain\Model\Territory;

class EloquentTerritoryRepository implements TerritoryRepositoryInterface
{
    public function findById(int $id): ?Territory
    {
        return Territory::find($id);
    }

    public function getAll(): Collection
    {
        return Territory::all();
    }

    public function save(Territory $territory): Territory
    {
        $territory->save();
        return $territory;
    }

    public function delete(Territory $territory): bool
    {
        return $territory->delete();
    }
}