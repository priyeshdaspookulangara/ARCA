<?php

namespace Modules\GRC\ProcessControl\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\GRC\ProcessControl\Domain\SoDRuleRepositoryInterface;
use Modules\GRC\ProcessControl\Domain\Model\SoDRule;

class EloquentSoDRuleRepository implements SoDRuleRepositoryInterface
{
    public function findById(int $id): ?SoDRule
    {
        return SoDRule::find($id);
    }

    public function getAll(): Collection
    {
        return SoDRule::all();
    }

    public function save(SoDRule $soDRule): SoDRule
    {
        $soDRule->save();
        return $soDRule;
    }

    public function delete(SoDRule $soDRule): bool
    {
        return $soDRule->delete();
    }
}