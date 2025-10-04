<?php

namespace Modules\Fina\CO\PCA\Infrastructure;

use Modules\Fina\CO\PCA\Domain\ProfitCenter;
use Modules\Fina\CO\PCA\Domain\Repositories\ProfitCenterRepository;
use Illuminate\Support\Collection;

class ProfitCenterRepositoryImpl implements ProfitCenterRepository
{
    public function findById(int $id): ?ProfitCenter
    {
        return ProfitCenter::find($id);
    }

    public function getAll(): Collection
    {
        return ProfitCenter::all();
    }

    public function save(ProfitCenter $profitCenter): void
    {
        $profitCenter->save();
    }

    public function delete(ProfitCenter $profitCenter): void
    {
        $profitCenter->delete();
    }
}