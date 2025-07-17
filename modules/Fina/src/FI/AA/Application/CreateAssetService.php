<?php

namespace Modules\Fina\FI\AA\Application;

use Modules\Fina\FI\AA\Domain\Repositories\AssetMasterRepositoryInterface;

class CreateAssetService
{
    private $assetMasterRepository;

    public function __construct(AssetMasterRepositoryInterface $assetMasterRepository)
    {
        $this->assetMasterRepository = $assetMasterRepository;
    }

    public function __invoke(array $data)
    {
        // Simplified example
        return $this->assetMasterRepository->create($data);
    }
}
