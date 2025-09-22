<?php

namespace Modules\Fina\FI\BL\Domain\Repositories;

use Modules\Fina\FI\BL\Domain\Entities\BankAccount;

interface BankAccountRepositoryInterface
{
    public function create(array $data): BankAccount;
    public function find(int $id): ?BankAccount;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
