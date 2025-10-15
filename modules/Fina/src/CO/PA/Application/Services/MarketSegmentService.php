<?php

namespace Modules\Fina\CO\PA\Application\Services;

use Modules\Fina\CO\PA\Domain\Repositories\MarketSegmentRepository;

class MarketSegmentService
{
    protected $marketSegmentRepository;

    public function __construct(MarketSegmentRepository $marketSegmentRepository)
    {
        $this->marketSegmentRepository = $marketSegmentRepository;
    }

    public function getAllMarketSegments()
    {
        return $this->marketSegmentRepository->getAll();
    }

    public function getMarketSegmentById(int $id)
    {
        return $this->marketSegmentRepository->findById($id);
    }

    public function createMarketSegment(array $data)
    {
        // Add any business logic/validation here before creating
        return $this->marketSegmentRepository->create($data);
    }

    public function updateMarketSegment(int $id, array $data)
    {
        // Add any business logic/validation here before updating
        return $this->marketSegmentRepository->update($id, $data);
    }

    public function deleteMarketSegment(int $id)
    {
        // Add any business logic/validation here before deleting
        return $this->marketSegmentRepository->delete($id);
    }
}