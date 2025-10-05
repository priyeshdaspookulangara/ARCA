<?php

namespace Modules\Fina\CO\PC\Domain;

use Modules\Fina\CO\PC\Domain\Repositories\ActivityTypeRepository;

class ActivityTypeService
{
    private ActivityTypeRepository $activityTypeRepository;

    public function __construct(ActivityTypeRepository $activityTypeRepository)
    {
        $this->activityTypeRepository = $activityTypeRepository;
    }

    public function createActivityType(array $data): ActivityType
    {
        $activityType = new ActivityType($data);
        $this->activityTypeRepository->save($activityType);
        return $activityType;
    }

    public function getActivityType(int $id): ?ActivityType
    {
        return $this->activityTypeRepository->findById($id);
    }

    public function getAllActivityTypes()
    {
        return $this->activityTypeRepository->getAll();
    }

    public function updateActivityType(int $id, array $data): ?ActivityType
    {
        $activityType = $this->activityTypeRepository->findById($id);
        if ($activityType) {
            $activityType->fill($data);
            $this->activityTypeRepository->save($activityType);
        }
        return $activityType;
    }

    public function deleteActivityType(int $id): bool
    {
        $activityType = $this->activityTypeRepository->findById($id);
        if ($activityType) {
            $this->activityTypeRepository->delete($activityType);
            return true;
        }
        return false;
    }
}