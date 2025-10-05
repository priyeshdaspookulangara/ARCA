<?php

namespace Modules\Fina\CO\PC\Domain;

use Modules\Fina\CO\PC\Domain\Repositories\CostElementRepository;

class CostElementService
{
    private CostElementRepository $costElementRepository;

    public function __construct(CostElementRepository $costElementRepository)
    {
        $this->costElementRepository = $costElementRepository;
    }

    public function createCostElement(array $data): CostElement
    {
        $costElement = new CostElement($data);
        $this->costElementRepository->save($costElement);
        return $costElement;
    }

    public function getCostElement(int $id): ?CostElement
    {
        return $this->costElementRepository->findById($id);
    }

    public function getAllCostElements()
    {
        return $this->costElementRepository->getAll();
    }

    public function updateCostElement(int $id, array $data): ?CostElement
    {
        $costElement = $this->costElementRepository->findById($id);
        if ($costElement) {
            $costElement->fill($data);
            $this->costElementRepository->save($costElement);
        }
        return $costElement;
    }

    public function deleteCostElement(int $id): bool
    {
        $costElement = $this->costElementRepository->findById($id);
        if ($costElement) {
            $this->costElementRepository->delete($costElement);
            return true;
        }
        return false;
    }
}