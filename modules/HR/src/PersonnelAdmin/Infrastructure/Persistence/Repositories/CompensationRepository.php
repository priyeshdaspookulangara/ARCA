<?php

namespace Modules\HR\PersonnelAdmin\Infrastructure\Persistence\Repositories;

use Modules\HR\Models\EmployeeCompensation;
use Modules\HR\PersonnelAdmin\Domain\Repositories\CompensationRepositoryInterface;
use Carbon\Carbon;

class CompensationRepository implements CompensationRepositoryInterface
{
    public function findCurrentByEmployeeId(int $employeeId, string $date = 'today')
    {
        $date = Carbon::parse($date);

        return EmployeeCompensation::where('employee_id', $employeeId)
            ->where('valid_from', '<=', $date)
            ->where('valid_to', '>=', $date)
            ->first();
    }

    public function findAllByEmployeeId(int $employeeId)
    {
        return EmployeeCompensation::where('employee_id', $employeeId)
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
        return EmployeeCompensation::create($data);
    }
}
