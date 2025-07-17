<?php

namespace Modules\Fina\FI\AA\Infrastructure\Persistence;

use Modules\Fina\FI\AA\Domain\Entities\AssetMaster;
use Modules\Fina\FI\AA\Domain\Repositories\AssetMasterRepositoryInterface;

class EloquentAssetMasterRepository implements AssetMasterRepositoryInterface
{
    public function create(array $data): AssetMaster
    {
        return AssetMaster::create($data);
    }

    public function find(int $id): ?AssetMaster
    {
        return AssetMaster::find($id);
    }
}
