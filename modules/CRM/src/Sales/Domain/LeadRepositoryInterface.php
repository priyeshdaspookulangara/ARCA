<?php

namespace Modules\CRM\Sales\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\Sales\Domain\Model\Lead;

interface LeadRepositoryInterface
{
    public function findById(int $id): ?Lead;

    public function getAll(): Collection;

    public function save(Lead $lead): Lead;

    public function delete(Lead $lead): bool;
}