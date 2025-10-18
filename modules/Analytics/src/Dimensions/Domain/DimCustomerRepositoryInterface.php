<?php

namespace Modules\Analytics\Dimensions\Domain;

use Illuminate\Support\Collection;
use Modules\Analytics\Dimensions\Domain\Model\DimCustomer;

interface DimCustomerRepositoryInterface
{
    public function findById(int $id): ?DimCustomer;

    public function getAll(): Collection;

    public function save(DimCustomer $dimCustomer): DimCustomer;

    public function delete(DimCustomer $dimCustomer): bool;
}