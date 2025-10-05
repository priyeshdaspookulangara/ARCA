<?php

namespace Modules\HR\OrganizationalManagement\Application\Services;

use Modules\HR\OrganizationalManagement\Domain\Entities\Position;
use Modules\HR\OrganizationalManagement\Domain\Repositories\PositionRepositoryInterface;

class PositionService
{
    private $positionRepository;

    public function __construct(PositionRepositoryInterface $positionRepository)
    {
        $this->positionRepository = $positionRepository;
    }

    public function createPosition(string $jobId, string $orgUnitId): Position
    {
        $id = uniqid('pos_');
        $position = new Position($id, $jobId, $orgUnitId);
        $this->positionRepository->save($position);
        return $position;
    }

    public function getPosition(string $id): ?Position
    {
        return $this->positionRepository->findById($id);
    }

    public function getAllPositions(): array
    {
        return $this->positionRepository->findAll();
    }

    public function deletePosition(string $id): void
    {
        $this->positionRepository->delete($id);
    }
}