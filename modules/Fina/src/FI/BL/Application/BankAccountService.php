<?php

namespace Modules\Fina\FI\BL\Application;

use Modules\Fina\FI\BL\Domain\Repositories\BankAccountRepositoryInterface;

class BankAccountService
{
    private $bankAccountRepository;

    public function __construct(BankAccountRepositoryInterface $bankAccountRepository)
    {
        $this->bankAccountRepository = $bankAccountRepository;
    }

    public function createBankAccount(array $data)
    {
        return $this->bankAccountRepository->create($data);
    }

    public function getBankAccount(int $id)
    {
        return $this->bankAccountRepository->find($id);
    }

    public function updateBankAccount(int $id, array $data)
    {
        return $this->bankAccountRepository->update($id, $data);
    }

    public function deleteBankAccount(int $id)
    {
        return $this->bankAccountRepository->delete($id);
    }
}
