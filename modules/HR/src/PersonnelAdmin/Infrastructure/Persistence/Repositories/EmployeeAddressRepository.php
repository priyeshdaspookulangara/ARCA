<?php

namespace Modules\HR\PersonnelAdmin\Infrastructure\Persistence\Repositories;

use Modules\HR\Models\EmployeeAddress;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeAddressRepositoryInterface;
use Carbon\Carbon;

class EmployeeAddressRepository implements EmployeeAddressRepositoryInterface
{
    public function findCurrentByEmployeeId(int $employeeId, string $addressType, string $date = 'today')
    {
        $date = Carbon::parse($date);

        return EmployeeAddress::where('employee_id', $employeeId)
            ->where('address_type', $addressType)
            ->where('valid_from', '<=', $date)
            ->where('valid_to', '>=', $date)
            ->first();
    }

    public function findAllByEmployeeId(int $employeeId, string $addressType)
    {
        return EmployeeAddress::where('employee_id', $employeeId)
            ->where('address_type', $addressType)
            ->orderBy('valid_from', 'desc')
            ->get();
    }

    public function delimitCurrentRecord(int $employeeId, string $addressType, string $newValidToDate)
    {
        $newValidToDate = Carbon::parse($newValidToDate);
        $currentRecord = $this->findCurrentByEmployeeId($employeeId, $addressType);

        if ($currentRecord) {
            $currentRecord->valid_to = $newValidToDate;
            $currentRecord->save();
        }
    }

    public function insertNewSlice(array $data)
    {
        return EmployeeAddress::create($data);
    }
}
