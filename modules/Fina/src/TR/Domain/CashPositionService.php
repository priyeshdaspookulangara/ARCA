<?php

namespace Modules\Fina\TR\Domain;

use Modules\Fina\TR\Domain\Repositories\CashPositionRepository;

class CashPositionService
{
    private CashPositionRepository $cashPositionRepository;

    public function __construct(CashPositionRepository $cashPositionRepository)
    {
        $this->cashPositionRepository = $cashPositionRepository;
    }

    public function createCashPosition(array $data): CashPosition
    {
        $cashPosition = new CashPosition($data);
        $this->cashPositionRepository->save($cashPosition);
        return $cashPosition;
    }

    public function getCashPosition(int $id): ?CashPosition
    {
        return $this->cashPositionRepository->findById($id);
    }

    public function getCashPositionByDate(\DateTime $date): ?CashPosition
    {
        return $this->cashPositionRepository->findByDate($date);
    }

    public function getAllCashPositions()
    {
        return $this->cashPositionRepository->getAll();
    }

    public function updateCashPosition(int $id, array $data): ?CashPosition
    {
        $cashPosition = $this->cashPositionRepository->findById($id);
        if ($cashPosition) {
            $cashPosition->fill($data);
            $this->cashPositionRepository->save($cashPosition);
        }
        return $cashPosition;
    }

    public function deleteCashPosition(int $id): bool
    {
        $cashPosition = $this->cashPositionRepository->findById($id);
        if ($cashPosition) {
            $this->cashPositionRepository->delete($cashPosition);
            return true;
        }
        return false;
    }
}