<?php

namespace Modules\HR\PersonnelAdmin\Domain\Repositories;

interface EmployeeAddressRepositoryInterface
{
    public function findCurrentByEmployeeId(int $employeeId, string $addressType, string $date = 'today');

    public function findAllByEmployeeId(int $employeeId, string $addressType);

    public function delimitCurrentRecord(int $employeeId, string $addressType, string $newValidToDate);

    public function insertNewSlice(array $data);
}
