<?php

namespace Modules\Fina\CO\PA\Domain\Repositories;

use Modules\Fina\CO\PA\Domain\MarketSegment;
use Illuminate\Support\Collection;

interface MarketSegmentRepository
{
    public function findById(int $id): ?MarketSegment;

    public function getAll(): Collection;

    public function save(MarketSegment $marketSegment): void;

    public function delete(MarketSegment $marketSegment): void;
}