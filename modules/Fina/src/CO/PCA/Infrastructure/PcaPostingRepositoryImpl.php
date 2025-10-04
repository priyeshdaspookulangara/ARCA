<?php

namespace Modules\Fina\CO\PCA\Infrastructure;

use Modules\Fina\CO\PCA\Domain\PcaPosting;
use Modules\Fina\CO\PCA\Domain\Repositories\PcaPostingRepository;
use Illuminate\Support\Collection;

class PcaPostingRepositoryImpl implements PcaPostingRepository
{
    public function findById(int $id): ?PcaPosting
    {
        return PcaPosting::find($id);
    }

    public function getByProfitCenterId(int $profitCenterId): Collection
    {
        return PcaPosting::where('profit_center_id', $profitCenterId)->get();
    }

    public function save(PcaPosting $pcaPosting): void
    {
        $pcaPosting->save();
    }

    public function delete(PcaPosting $pcaPosting): void
    {
        $pcaPosting->delete();
    }
}