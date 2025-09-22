<?php

namespace Modules\Fina\FI\BL\Application;

use Modules\Fina\FI\BL\Domain\Repositories\BankStatementRepositoryInterface;

class BankStatementService
{
    private $bankStatementRepository;

    public function __construct(BankStatementRepositoryInterface $bankStatementRepository)
    {
        $this->bankStatementRepository = $bankStatementRepository;
    }

    public function createBankStatement(array $data)
    {
        return $this->bankStatementRepository->create($data);
    }

    public function getBankStatement(int $id)
    {
        return $this->bankStatementRepository->find($id);
    }

    public function updateBankStatement(int $id, array $data)
    {
        return $this->bankStatementRepository->update($id, $data);
    }

    public function deleteBankStatement(int $id)
    {
        return $this->bankStatementRepository->delete($id);
    }
}
