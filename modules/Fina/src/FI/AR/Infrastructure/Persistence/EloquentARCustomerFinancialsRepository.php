<?php

namespace Modules\Fina\FI\AR\Infrastructure\Persistence;

use Modules\Fina\FI\AR\Domain\Entities\ARCustomerFinancials;
use Modules\Fina\FI\AR\Domain\Repositories\ARCustomerFinancialsRepositoryInterface;

class EloquentARCustomerFinancialsRepository implements ARCustomerFinancialsRepositoryInterface
{
    public function create(array $data): ARCustomerFinancials
    {
        return ARCustomerFinancials::create($data);
    }

    public function findByCustomerAndCompany(int $customerId, int $companyCodeId): ?ARCustomerFinancials
    {
        return ARCustomerFinancials::where('customer_id', $customerId)
            ->where('company_code_id', $companyCodeId)
            ->first();
    }

    public function findAll(): \Illuminate\Support\Collection
    {
        return ARCustomerFinancials::all();
    }
}
