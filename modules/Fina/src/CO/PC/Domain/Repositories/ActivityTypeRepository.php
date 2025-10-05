<?php

namespace Modules\Fina\CO\PC\Domain\Repositories;

use Modules\Fina\CO\PC\Domain\ActivityType;
use Illuminate\Support\Collection;

interface ActivityTypeRepository
{
    public function findById(int $id): ?ActivityType;

    public function getAll(): Collection;

    public function save(ActivityType $activityType): void;

    public function delete(ActivityType $activityType): void;
}