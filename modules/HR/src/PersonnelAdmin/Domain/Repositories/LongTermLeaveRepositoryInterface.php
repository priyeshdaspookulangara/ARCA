<?php

namespace Modules\HR\PersonnelAdmin\Domain\Repositories;

interface LongTermLeaveRepositoryInterface
{
    public function findByEmployeeId(int $employeeId);

    public function create(array $data);

    public function update(int $id, array $data);
}
