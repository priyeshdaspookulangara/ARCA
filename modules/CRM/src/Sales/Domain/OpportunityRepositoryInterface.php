<?php

namespace Modules\CRM\Sales\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\Sales\Domain\Model\Opportunity;

interface OpportunityRepositoryInterface
{
    public function findById(int $id): ?Opportunity;

    public function getAll(): Collection;

    public function save(Opportunity $opportunity): Opportunity;

    public function delete(Opportunity $opportunity): bool;
}