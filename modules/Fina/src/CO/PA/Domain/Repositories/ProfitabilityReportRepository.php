<?php

namespace Modules\Fina\CO\PA\Domain\Repositories;

interface ProfitabilityReportRepository
{
    public function getAll();
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findByMarketSegment(int $marketSegmentId);
}