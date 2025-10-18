<?php

namespace Modules\GRC\ProcessControl\Domain;

use Illuminate\Support\Collection;
use Modules\GRC\ProcessControl\Domain\Model\SoDRule;

interface SoDRuleRepositoryInterface
{
    public function findById(int $id): ?SoDRule;

    public function getAll(): Collection;

    public function save(SoDRule $soDRule): SoDRule;

    public function delete(SoDRule $soDRule): bool;
}