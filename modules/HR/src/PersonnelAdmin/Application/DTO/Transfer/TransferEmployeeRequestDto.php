<?php

namespace Modules\HR\PersonnelAdmin\Application\DTO\Transfer;

class TransferEmployeeRequestDto
{
    public function __construct(
        public readonly int $employeeId,
        public readonly int $actionRequestId,
        public readonly string $effectiveDate,
        public readonly int $newPositionId,
        public readonly int $newJobTitleId,
        public readonly int $newDepartmentId,
        public readonly int $newCostCenterId,
        public readonly int $newCompanyCodeId,
        public readonly int $newPersonnelAreaId,
        public readonly int $newPersonnelSubAreaId,
        public readonly int $newEmployeeGroupId,
        public readonly int $newEmployeeSubGroupId,
        public readonly int $newManagerCoreUserId,
        public readonly int $newEmploymentStatusId,
    ) {
    }
}
