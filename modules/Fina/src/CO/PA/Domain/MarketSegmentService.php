<?php

namespace Modules\Fina\CO\PA\Domain;

use Modules\Fina\CO\PA\Domain\Repositories\MarketSegmentRepository;

class MarketSegmentService
{
    private MarketSegmentRepository $marketSegmentRepository;

    public function __construct(MarketSegmentRepository $marketSegmentRepository)
    {
        $this->marketSegmentRepository = $marketSegmentRepository;
    }

    public function createMarketSegment(array $data): MarketSegment
    {
        $marketSegment = new MarketSegment($data);
        $this->marketSegmentRepository->save($marketSegment);
        return $marketSegment;
    }

    public function getMarketSegment(int $id): ?MarketSegment
    {
        return $this->marketSegmentRepository->findById($id);
    }

    public function getAllMarketSegments()
    {
        return $this->marketSegmentRepository->getAll();
    }

    public function updateMarketSegment(int $id, array $data): ?MarketSegment
    {
        $marketSegment = $this->marketSegmentRepository->findById($id);
        if ($marketSegment) {
            $marketSegment->fill($data);
            $this->marketSegmentRepository->save($marketSegment);
        }
        return $marketSegment;
    }

    public function deleteMarketSegment(int $id): bool
    {
        $marketSegment = $this->marketSegmentRepository->findById($id);
        if ($marketSegment) {
            $this->marketSegmentRepository->delete($marketSegment);
            return true;
        }
        return false;
    }
}