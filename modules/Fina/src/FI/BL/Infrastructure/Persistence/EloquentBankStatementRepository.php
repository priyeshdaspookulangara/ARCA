<?php

namespace Modules\Fina\FI\BL\Infrastructure\Persistence;

use Modules\Fina\FI\BL\Domain\Entities\BankStatement;
use Modules\Fina\FI\BL\Domain\Repositories\BankStatementRepositoryInterface;

class EloquentBankStatementRepository implements BankStatementRepositoryInterface
{
    public function create(array $data): BankStatement
    {
        return BankStatement::create($data);
    }

    public function find(int $id): ?BankStatement
    {
        return BankStatement::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return BankStatement::find($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return BankStatement::find($id)->delete();
    }
}
