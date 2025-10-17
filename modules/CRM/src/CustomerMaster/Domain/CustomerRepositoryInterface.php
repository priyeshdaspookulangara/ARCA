<?php

namespace Modules\CRM\CustomerMaster\Domain;

use Illuminate\Support\Collection;
use Modules\CRM\CustomerMaster\Domain\Model\Customer;

interface CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer;

    public function getAll(): Collection;

    public function save(Customer $customer): Customer;

    public function delete(Customer $customer): bool;
}