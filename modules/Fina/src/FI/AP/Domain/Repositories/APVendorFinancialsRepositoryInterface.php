<?php

namespace Modules\Fina\FI\AP\Domain\Repositories;

use Modules\Fina\FI\AP\Domain\Entities\APVendorFinancials;

interface APVendorFinancialsRepositoryInterface
{
    public function create(array $data): APVendorFinancials;
    public function findByVendorAndCompany(int $vendorId, int $companyCodeId): ?APVendorFinancials;
}
