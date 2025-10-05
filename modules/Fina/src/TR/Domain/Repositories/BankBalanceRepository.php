<?php

namespace Modules\Fina\TR\Domain\Repositories;

use Modules\Fina\TR\Domain\BankBalance;
use Illuminate\Support\Collection;

interface BankBalanceRepository
{
    public function findById(int $id): ?BankBalance;

    public function findByAccountIdAndDate(int $bankAccountId, \DateTime $date): ?BankBalance;

    public function getAll(): Collection;

    public function save(BankBalance $bankBalance): void;

    public function delete(BankBalance $bankBalance): void;
}