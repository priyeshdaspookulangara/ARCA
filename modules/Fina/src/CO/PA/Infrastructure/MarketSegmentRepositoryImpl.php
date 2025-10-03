<?php

namespace Modules\Fina\CO\PA\Infrastructure;

use Modules\Fina\CO\PA\Domain\MarketSegment;
use Modules\Fina\CO\PA\Domain\Repositories\MarketSegmentRepository;
use Illuminate\Support\Collection;

class MarketSegmentRepositoryImpl implements MarketSegmentRepository
{
    public function findById(int $id): ?MarketSegment
    {
        return MarketSegment::find($id);
    }

    public function getAll(): Collection
    {
        return MarketSegment::all();
    }

    public function save(MarketSegment $marketSegment): void
    {
        $marketSegment->save();
    }

    public function delete(MarketSegment $marketSegment): void
    {
        $marketSegment->delete();
    }
}