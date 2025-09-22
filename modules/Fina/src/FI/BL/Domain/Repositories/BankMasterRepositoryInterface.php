<?php

namespace Modules\Fina\FI\BL\Domain\Repositories;

use Modules\Fina\FI\BL\Domain\Entities\BankMaster;

interface BankMasterRepositoryInterface
{
    public function create(array $data): BankMaster;
    public function find(int $id): ?BankMaster;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
