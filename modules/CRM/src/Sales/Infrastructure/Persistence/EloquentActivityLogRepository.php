<?php

namespace Modules\CRM\Sales\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\CRM\Sales\Domain\ActivityLogRepositoryInterface;
use Modules\CRM\Sales\Domain\Model\ActivityLog;

class EloquentActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function findById(int $id): ?ActivityLog
    {
        return ActivityLog::find($id);
    }

    public function getAll(): Collection
    {
        return ActivityLog::all();
    }

    public function save(ActivityLog $activityLog): ActivityLog
    {
        $activityLog->save();
        return $activityLog;
    }

    public function delete(ActivityLog $activityLog): bool
    {
        return $activityLog->delete();
    }
}