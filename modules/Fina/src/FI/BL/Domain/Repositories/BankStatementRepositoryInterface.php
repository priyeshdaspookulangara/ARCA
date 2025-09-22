<?php

namespace Modules\Fina\FI\BL\Domain\Repositories;

use Modules\Fina\FI\BL\Domain\Entities\BankStatement;

interface BankStatementRepositoryInterface
{
    public function create(array $data): BankStatement;
    public function find(int $id): ?BankStatement;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
