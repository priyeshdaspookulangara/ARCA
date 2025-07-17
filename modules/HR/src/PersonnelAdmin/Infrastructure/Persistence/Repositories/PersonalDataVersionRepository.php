<?php

namespace Modules\HR\PersonnelAdmin\Infrastructure\Persistence\Repositories;

use Modules\HR\Models\EmployeePersonalDataVersion;
use Modules\HR\PersonnelAdmin\Domain\Repositories\PersonalDataVersionRepositoryInterface;
use Carbon\Carbon;

class PersonalDataVersionRepository implements PersonalDataVersionRepositoryInterface
{
    public function findCurrentByEmployeeId(int $employeeId, string $date = 'today')
    {
        $date = Carbon::parse($date);

        return EmployeePersonalDataVersion::where('employee_id', $employeeId)
            ->where('valid_from', '<=', $date)
            ->where('valid_to', '>=', $date)
            ->first();
    }

    public function findAllByEmployeeId(int $employeeId)
    {
        return EmployeePersonalDataVersion::where('employee_id', $employeeId)
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
        return EmployeePersonalDataVersion::create($data);
    }
}
