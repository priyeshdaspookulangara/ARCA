<?php

namespace Modules\Fina\TR\Domain;

use Modules\Fina\TR\Domain\Repositories\BankBalanceRepository;

class BankBalanceService
{
    private BankBalanceRepository $bankBalanceRepository;

    public function __construct(BankBalanceRepository $bankBalanceRepository)
    {
        $this->bankBalanceRepository = $bankBalanceRepository;
    }

    public function createBankBalance(array $data): BankBalance
    {
        $bankBalance = new BankBalance($data);
        $this->bankBalanceRepository->save($bankBalance);
        return $bankBalance;
    }

    public function getBankBalance(int $id): ?BankBalance
    {
        return $this->bankBalanceRepository->findById($id);
    }

    public function getBankBalanceByAccountAndDate(int $bankAccountId, \DateTime $date): ?BankBalance
    {
        return $this->bankBalanceRepository->findByAccountIdAndDate($bankAccountId, $date);
    }

    public function getAllBankBalances()
    {
        return $this->bankBalanceRepository->getAll();
    }

    public function updateBankBalance(int $id, array $data): ?BankBalance
    {
        $bankBalance = $this->bankBalanceRepository->findById($id);
        if ($bankBalance) {
            $bankBalance->fill($data);
            $this->bankBalanceRepository->save($bankBalance);
        }
        return $bankBalance;
    }

    public function deleteBankBalance(int $id): bool
    {
        $bankBalance = $this->bankBalanceRepository->findById($id);
        if ($bankBalance) {
            $this->bankBalanceRepository->delete($bankBalance);
            return true;
        }
        return false;
    }
}