<?php

namespace Modules\Fina\FI\BL\Infrastructure\Persistence;

use Modules\Fina\FI\BL\Domain\Entities\BankMaster;
use Modules\Fina\FI\BL\Domain\Repositories\BankMasterRepositoryInterface;

class EloquentBankMasterRepository implements BankMasterRepositoryInterface
{
    public function create(array $data): BankMaster
    {
        return BankMaster::create($data);
    }

    public function find(int $id): ?BankMaster
    {
        return BankMaster::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return BankMaster::find($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return BankMaster::find($id)->delete();
    }
}
