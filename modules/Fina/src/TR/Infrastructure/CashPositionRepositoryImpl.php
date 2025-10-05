<?php

namespace Modules\Fina\TR\Infrastructure;

use Modules\Fina\TR\Domain\CashPosition;
use Modules\Fina\TR\Domain\Repositories\CashPositionRepository;
use Illuminate\Support\Collection;

class CashPositionRepositoryImpl implements CashPositionRepository
{
    public function findById(int $id): ?CashPosition
    {
        return CashPosition::find($id);
    }

    public function findByDate(\DateTime $date): ?CashPosition
    {
        return CashPosition::where('position_date', $date->format('Y-m-d'))->first();
    }

    public function getAll(): Collection
    {
        return CashPosition::all();
    }

    public function save(CashPosition $cashPosition): void
    {
        $cashPosition->save();
    }

    public function delete(CashPosition $cashPosition): void
    {
        $cashPosition->delete();
    }
}