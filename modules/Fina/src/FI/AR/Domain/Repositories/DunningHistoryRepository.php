<?php

namespace Modules\Fina\FI\AR\Domain\Repositories;

use Modules\Fina\FI\AR\Domain\Entities\DunningHistory;
use Illuminate\Support\Collection;

interface DunningHistoryRepository
{
    public function findById(int $id): ?DunningHistory;

    public function getByCustomerId(int $customerId): Collection;

    public function save(DunningHistory $dunningHistory): void;
}