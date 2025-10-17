<?php

namespace Modules\CRM\CustomerMaster\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\CRM\CustomerMaster\Domain\CustomerRepositoryInterface;
use Modules\CRM\CustomerMaster\Domain\Model\Customer;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function getAll(): Collection
    {
        return Customer::all();
    }

    public function save(Customer $customer): Customer
    {
        $customer->save();
        return $customer;
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }
}