<?php

namespace Modules\Fina\CO\PA\Infrastructure\Repositories;

use Modules\Fina\CO\PA\Domain\Entities\MarketSegment;
use Modules\Fina\CO\PA\Domain\Repositories\MarketSegmentRepository;

class EloquentMarketSegmentRepository implements MarketSegmentRepository
{
    public function getAll()
    {
        return MarketSegment::all();
    }

    public function findById(int $id)
    {
        return MarketSegment::find($id);
    }

    public function create(array $data)
    {
        return MarketSegment::create($data);
    }

    public function update(int $id, array $data)
    {
        $market_segment = MarketSegment::findOrFail($id);
        $market_segment->update($data);
        return $market_segment;
    }

    public function delete(int $id)
    {
        $market_segment = MarketSegment::findOrFail($id);
        $market_segment->delete();
    }
}