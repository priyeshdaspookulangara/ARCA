<?php

namespace Modules\Analytics\Dimensions\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\Analytics\Dimensions\Domain\DimCustomerRepositoryInterface;
use Modules\Analytics\Dimensions\Domain\Model\DimCustomer;

class EloquentDimCustomerRepository implements DimCustomerRepositoryInterface
{
    public function findById(int $id): ?DimCustomer
    {
        return DimCustomer::find($id);
    }

    public function getAll(): Collection
    {
        return DimCustomer::all();
    }

    public function save(DimCustomer $dimCustomer): DimCustomer
    {
        $dimCustomer->save();
        return $dimCustomer;
    }

    public function delete(DimCustomer $dimCustomer): bool
    {
        return $dimCustomer->delete();
    }
}