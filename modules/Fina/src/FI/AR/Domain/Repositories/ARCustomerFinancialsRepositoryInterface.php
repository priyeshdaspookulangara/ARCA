<?php

namespace Modules\Fina\FI\AR\Domain\Repositories;

use Modules\Fina\FI\AR\Domain\Entities\ARCustomerFinancials;

use Illuminate\Support\Collection;

interface ARCustomerFinancialsRepositoryInterface
{
    public function create(array $data): ARCustomerFinancials;
    public function findByCustomerAndCompany(int $customerId, int $companyCodeId): ?ARCustomerFinancials;
    public function findAll(): Collection;
}
