<?php

namespace Modules\Fina\FI\AR\Infrastructure\Persistence;

use Modules\Fina\FI\AR\Domain\Entities\DunningHistory;
use Modules\Fina\FI\AR\Domain\Repositories\DunningHistoryRepository;
use Illuminate\Support\Collection;

class DunningHistoryRepositoryImpl implements DunningHistoryRepository
{
    public function findById(int $id): ?DunningHistory
    {
        return DunningHistory::find($id);
    }

    public function getByCustomerId(int $customerId): Collection
    {
        return DunningHistory::where('customer_id', $customerId)->get();
    }

    public function save(DunningHistory $dunningHistory): void
    {
        $dunningHistory->save();
    }
}