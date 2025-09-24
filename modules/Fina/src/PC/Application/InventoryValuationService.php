<?php

namespace Modules\Fina\PC\Application;

use Modules\Fina\PC\Domain\Repositories\InventoryValuationRepositoryInterface;

class InventoryValuationService
{
    private $inventoryValuationRepository;

    public function __construct(InventoryValuationRepositoryInterface $inventoryValuationRepository)
    {
        $this->inventoryValuationRepository = $inventoryValuationRepository;
    }

    public function createInventoryValuation(array $data)
    {
        return $this->inventoryValuationRepository->create($data);
    }

    public function getInventoryValuation(int $id)
    {
        return $this->inventoryValuationRepository->find($id);
    }

    public function updateInventoryValuation(int $id, array $data)
    {
        return $this->inventoryValuationRepository->update($id, $data);
    }

    public function deleteInventoryValuation(int $id)
    {
        return $this->inventoryValuationRepository->delete($id);
    }
}
