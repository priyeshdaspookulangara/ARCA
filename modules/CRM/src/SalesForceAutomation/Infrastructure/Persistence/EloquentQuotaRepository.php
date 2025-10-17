<?php

namespace Modules\CRM\SalesForceAutomation\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\CRM\SalesForceAutomation\Domain\QuotaRepositoryInterface;
use Modules\CRM\SalesForceAutomation\Domain\Model\Quota;

class EloquentQuotaRepository implements QuotaRepositoryInterface
{
    public function findById(int $id): ?Quota
    {
        return Quota::find($id);
    }

    public function getAll(): Collection
    {
        return Quota::all();
    }

    public function save(Quota $quota): Quota
    {
        $quota->save();
        return $quota;
    }

    public function delete(Quota $quota): bool
    {
        return $quota->delete();
    }
}