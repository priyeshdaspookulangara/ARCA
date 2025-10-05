<?php

namespace Modules\Fina\TR\Domain\Repositories;

use Modules\Fina\TR\Domain\CashPosition;
use Illuminate\Support\Collection;

interface CashPositionRepository
{
    public function findById(int $id): ?CashPosition;

    public function findByDate(\DateTime $date): ?CashPosition;

    public function getAll(): Collection;

    public function save(CashPosition $cashPosition): void;

    public function delete(CashPosition $cashPosition): void;
}