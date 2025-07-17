<?php

namespace Modules\HR\PersonnelAdmin\Domain\Repositories;

interface JobAssignmentRepositoryInterface
{
    public function findCurrentByEmployeeId(int $employeeId, string $date = 'today');

    public function findAllByEmployeeId(int $employeeId);

    public function delimitCurrentRecord(int $employeeId, string $newValidToDate);

    public function insertNewSlice(array $data);
}
