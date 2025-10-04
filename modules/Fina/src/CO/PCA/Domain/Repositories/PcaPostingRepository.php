<?php

namespace Modules\Fina\CO\PCA\Domain\Repositories;

use Modules\Fina\CO\PCA\Domain\PcaPosting;
use Illuminate\Support\Collection;

interface PcaPostingRepository
{
    public function findById(int $id): ?PcaPosting;

    public function getByProfitCenterId(int $profitCenterId): Collection;

    public function save(PcaPosting $pcaPosting): void;

    public function delete(PcaPosting $pcaPosting): void;
}