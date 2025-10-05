<?php

namespace Modules\Fina\CO\PC\Infrastructure;

use Modules\Fina\CO\PC\Domain\ActivityType;
use Modules\Fina\CO\PC\Domain\Repositories\ActivityTypeRepository;
use Illuminate\Support\Collection;

class ActivityTypeRepositoryImpl implements ActivityTypeRepository
{
    public function findById(int $id): ?ActivityType
    {
        return ActivityType::find($id);
    }

    public function getAll(): Collection
    {
        return ActivityType::all();
    }

    public function save(ActivityType $activityType): void
    {
        $activityType->save();
    }

    public function delete(ActivityType $activityType): void
    {
        $activityType->delete();
    }
}