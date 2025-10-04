<?php

namespace Modules\Fina\CO\PCA\Domain;

use Modules\Fina\CO\PCA\Domain\Repositories\PcaPostingRepository;

class PcaPostingService
{
    private PcaPostingRepository $pcaPostingRepository;

    public function __construct(PcaPostingRepository $pcaPostingRepository)
    {
        $this->pcaPostingRepository = $pcaPostingRepository;
    }

    public function createPosting(array $data): PcaPosting
    {
        $posting = new PcaPosting($data);
        $this->pcaPostingRepository->save($posting);
        return $posting;
    }

    public function getPosting(int $id): ?PcaPosting
    {
        return $this->pcaPostingRepository->findById($id);
    }

    public function getPostingsForProfitCenter(int $profitCenterId)
    {
        return $this->pcaPostingRepository->getByProfitCenterId($profitCenterId);
    }

    public function deletePosting(int $id): bool
    {
        $posting = $this->pcaPostingRepository->findById($id);
        if ($posting) {
            $this->pcaPostingRepository->delete($posting);
            return true;
        }
        return false;
    }
}