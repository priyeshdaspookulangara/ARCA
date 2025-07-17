<?php

namespace Modules\HR\PersonnelAdmin\Infrastructure\Persistence\Repositories;

use Modules\HR\Models\EmployeeJobAssignment;
use Modules\HR\PersonnelAdmin\Domain\Repositories\JobAssignmentRepositoryInterface;
use Carbon\Carbon;

class JobAssignmentRepository implements JobAssignmentRepositoryInterface
{
    public function findCurrentByEmployeeId(int $employeeId, string $date = 'today')
    {
        $date = Carbon::parse($date);

        return EmployeeJobAssignment::where('employee_id', $employeeId)
            ->where('valid_from', '<=', $date)
            ->where('valid_to', '>=', $date)
            ->first();
    }

    public function findAllByEmployeeId(int $employeeId)
    {
        return EmployeeJobAssignment::where('employee_id', $employeeId)
            ->orderBy('valid_from', 'desc')
            ->get();
    }

    public function delimitCurrentRecord(int $employeeId, string $newValidToDate)
    {
        $newValidToDate = Carbon::parse($newValidToDate);
        $currentRecord = $this->findCurrentByEmployeeId($employeeId);

        if ($currentRecord) {
            $currentRecord->valid_to = $newValidToDate;
            $currentRecord->save();
        }
    }

    public function insertNewSlice(array $data)
    {
        return EmployeeJobAssignment::create($data);
    }
}
