<?php

namespace Modules\CRM\Sales\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\Sales\Domain\Model\ActivityLog;

interface ActivityLogRepositoryInterface
{
    public function findById(int $id): ?ActivityLog;

    public function getAll(): Collection;

    public function save(ActivityLog $activityLog): ActivityLog;

    public function delete(ActivityLog $activityLog): bool;
}