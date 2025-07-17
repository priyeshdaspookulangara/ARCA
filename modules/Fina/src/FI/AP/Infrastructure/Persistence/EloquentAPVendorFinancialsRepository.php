<?php

namespace Modules\Fina\FI\AP\Infrastructure\Persistence;

use Modules\Fina\FI\AP\Domain\Entities\APVendorFinancials;
use Modules\Fina\FI\AP\Domain\Repositories\APVendorFinancialsRepositoryInterface;

class EloquentAPVendorFinancialsRepository implements APVendorFinancialsRepositoryInterface
{
    public function create(array $data): APVendorFinancials
    {
        return APVendorFinancials::create($data);
    }

    public function findByVendorAndCompany(int $vendorId, int $companyCodeId): ?APVendorFinancials
    {
        return APVendorFinancials::where('vendor_id', $vendorId)
            ->where('company_code_id', $companyCodeId)
            ->first();
    }
}
