<?php

namespace Modules\CRM\Sales\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\CRM\Sales\Domain\OpportunityRepositoryInterface;
use Modules\CRM\Sales\Domain\Model\Opportunity;

class EloquentOpportunityRepository implements OpportunityRepositoryInterface
{
    public function findById(int $id): ?Opportunity
    {
        return Opportunity::find($id);
    }

    public function getAll(): Collection
    {
        return Opportunity::all();
    }

    public function save(Opportunity $opportunity): Opportunity
    {
        $opportunity->save();
        return $opportunity;
    }

    public function delete(Opportunity $opportunity): bool
    {
        return $opportunity->delete();
    }
}