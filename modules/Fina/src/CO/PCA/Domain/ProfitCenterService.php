<?php

namespace Modules\Fina\CO\PCA\Domain;

use Modules\Fina\CO\PCA\Domain\Repositories\ProfitCenterRepository;

class ProfitCenterService
{
    private ProfitCenterRepository $profitCenterRepository;

    public function __construct(ProfitCenterRepository $profitCenterRepository)
    {
        $this->profitCenterRepository = $profitCenterRepository;
    }

    public function createProfitCenter(array $data): ProfitCenter
    {
        $profitCenter = new ProfitCenter($data);
        $this->profitCenterRepository->save($profitCenter);
        return $profitCenter;
    }

    public function getProfitCenter(int $id): ?ProfitCenter
    {
        return $this->profitCenterRepository->findById($id);
    }

    public function getAllProfitCenters()
    {
        return $this->profitCenterRepository->getAll();
    }

    public function updateProfitCenter(int $id, array $data): ?ProfitCenter
    {
        $profitCenter = $this->profitCenterRepository->findById($id);
        if ($profitCenter) {
            $profitCenter->fill($data);
            $this->profitCenterRepository->save($profitCenter);
        }
        return $profitCenter;
    }

    public function deleteProfitCenter(int $id): bool
    {
        $profitCenter = $this->profitCenterRepository->findById($id);
        if ($profitCenter) {
            $this->profitCenterRepository->delete($profitCenter);
            return true;
        }
        return false;
    }
}