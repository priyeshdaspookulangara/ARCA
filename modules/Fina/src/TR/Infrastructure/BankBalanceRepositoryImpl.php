<?php

namespace Modules\Fina\TR\Infrastructure;

use Modules\Fina\TR\Domain\BankBalance;
use Modules\Fina\TR\Domain\Repositories\BankBalanceRepository;
use Illuminate\Support\Collection;

class BankBalanceRepositoryImpl implements BankBalanceRepository
{
    public function findById(int $id): ?BankBalance
    {
        return BankBalance::find($id);
    }

    public function findByAccountIdAndDate(int $bankAccountId, \DateTime $date): ?BankBalance
    {
        return BankBalance::where('bank_account_id', $bankAccountId)
            ->where('balance_date', $date->format('Y-m-d'))
            ->first();
    }

    public function getAll(): Collection
    {
        return BankBalance::all();
    }

    public function save(BankBalance $bankBalance): void
    {
        $bankBalance->save();
    }

    public function delete(BankBalance $bankBalance): void
    {
        $bankBalance->delete();
    }
}