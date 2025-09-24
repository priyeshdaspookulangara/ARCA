<?php

namespace Modules\Fina\PC\Application;

use Modules\Fina\PC\Domain\Repositories\CostObjectControllingRepositoryInterface;

class CostObjectControllingService
{
    private $costObjectControllingRepository;

    public function __construct(CostObjectControllingRepositoryInterface $costObjectControllingRepository)
    {
        $this->costObjectControllingRepository = $costObjectControllingRepository;
    }

    public function createCostObjectControlling(array $data)
    {
        return $this->costObjectControllingRepository->create($data);
    }

    public function getCostObjectControlling(int $id)
    {
        return $this->costObjectControllingRepository->find($id);
    }

    public function updateCostObjectControlling(int $id, array $data)
    {
        return $this->costObjectControllingRepository->update($id, $data);
    }

    public function deleteCostObjectControlling(int $id)
    {
        return $this->costObjectControllingRepository->delete($id);
    }
}
