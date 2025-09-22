<?php

namespace Modules\Fina\FI\BL\Application;

use Modules\Fina\FI\BL\Domain\Repositories\BankMasterRepositoryInterface;

class BankMasterService
{
    private $bankMasterRepository;

    public function __construct(BankMasterRepositoryInterface $bankMasterRepository)
    {
        $this->bankMasterRepository = $bankMasterRepository;
    }

    public function createBankMaster(array $data)
    {
        return $this->bankMasterRepository->create($data);
    }

    public function getBankMaster(int $id)
    {
        return $this->bankMasterRepository->find($id);
    }

    public function updateBankMaster(int $id, array $data)
    {
        return $this->bankMasterRepository->update($id, $data);
    }

    public function deleteBankMaster(int $id)
    {
        return $this->bankMasterRepository->delete($id);
    }
}
