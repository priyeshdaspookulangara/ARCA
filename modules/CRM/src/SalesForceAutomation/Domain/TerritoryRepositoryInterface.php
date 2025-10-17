<?php

namespace Modules\CRM\SalesForceAutomation\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\SalesForceAutomation\Domain\Model\Territory;

interface TerritoryRepositoryInterface
{
    public function findById(int $id): ?Territory;

    public function getAll(): Collection;

    public function save(Territory $territory): Territory;

    public function delete(Territory $territory): bool;
}