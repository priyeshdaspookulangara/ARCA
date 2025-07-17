<?php

namespace Modules\HR\PersonnelAdmin\Infrastructure\Persistence\Repositories;

use Modules\HR\Models\EmployeeLongTermLeave;
use Modules\HR\PersonnelAdmin\Domain\Repositories\LongTermLeaveRepositoryInterface;

class LongTermLeaveRepository implements LongTermLeaveRepositoryInterface
{
    public function findByEmployeeId(int $employeeId)
    {
        return EmployeeLongTermLeave::where('employee_id', $employeeId)->get();
    }

    public function create(array $data)
    {
        return EmployeeLongTermLeave::create($data);
    }

    public function update(int $id, array $data)
    {
        $leave = EmployeeLongTermLeave::findOrFail($id);
        $leave->update($data);
        return $leave;
    }
}
