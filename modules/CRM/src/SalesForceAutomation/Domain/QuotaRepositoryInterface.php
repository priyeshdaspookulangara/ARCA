<?php

namespace Modules\CRM\SalesForceAutomation\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\SalesForceAutomation\Domain\Model\Quota;

interface QuotaRepositoryInterface
{
    public function findById(int $id): ?Quota;

    public function getAll(): Collection;

    public function save(Quota $quota): Quota;

    public function delete(Quota $quota): bool;
}