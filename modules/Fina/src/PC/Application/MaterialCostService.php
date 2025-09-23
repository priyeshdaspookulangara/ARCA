<?php

namespace Modules\Fina\PC\Application;

use Modules\Fina\PC\Domain\Repositories\MaterialCostRepositoryInterface;

class MaterialCostService
{
    private $materialCostRepository;

    public function __construct(MaterialCostRepositoryInterface $materialCostRepository)
    {
        $this->materialCostRepository = $materialCostRepository;
    }

    public function createMaterialCost(array $data)
    {
        return $this->materialCostRepository->create($data);
    }

    public function getMaterialCost(int $id)
    {
        return $this->materialCostRepository->find($id);
    }

    public function updateMaterialCost(int $id, array $data)
    {
        return $this->materialCostRepository->update($id, $data);
    }

    public function deleteMaterialCost(int $id)
    {
        return $this->materialCostRepository->delete($id);
    }
}
