<?php

namespace Modules\CRM\Sales\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\CRM\Sales\Domain\LeadRepositoryInterface;
use Modules\CRM\Sales\Domain\Model\Lead;

class EloquentLeadRepository implements LeadRepositoryInterface
{
    public function findById(int $id): ?Lead
    {
        return Lead::find($id);
    }

    public function getAll(): Collection
    {
        return Lead::all();
    }

    public function save(Lead $lead): Lead
    {
        $lead->save();
        return $lead;
    }

    public function delete(Lead $lead): bool
    {
        return $lead->delete();
    }
}