<?php

namespace Modules\Fina\FI\BL\Infrastructure\Persistence;

use Modules\Fina\FI\BL\Domain\Entities\BankAccount;
use Modules\Fina\FI\BL\Domain\Repositories\BankAccountRepositoryInterface;

class EloquentBankAccountRepository implements BankAccountRepositoryInterface
{
    public function create(array $data): BankAccount
    {
        return BankAccount::create($data);
    }

    public function find(int $id): ?BankAccount
    {
        return BankAccount::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return BankAccount::find($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return BankAccount::find($id)->delete();
    }
}
