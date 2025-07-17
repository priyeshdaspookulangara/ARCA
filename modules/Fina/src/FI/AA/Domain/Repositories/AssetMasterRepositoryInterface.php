<?php

namespace Modules\Fina\FI\AA\Domain\Repositories;

use Modules\Fina\FI\AA\Domain\Entities\AssetMaster;

interface AssetMasterRepositoryInterface
{
    public function create(array $data): AssetMaster;
    public function find(int $id): ?AssetMaster;
}
