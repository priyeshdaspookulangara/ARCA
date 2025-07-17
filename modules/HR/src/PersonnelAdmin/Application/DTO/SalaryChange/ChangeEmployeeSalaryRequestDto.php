<?php

namespace Modules\HR\PersonnelAdmin\Application\DTO\SalaryChange;

class ChangeEmployeeSalaryRequestDto
{
    public function __construct(
        public readonly int $employeeId,
        public readonly int $actionRequestId,
        public readonly string $effectiveDate,
        public readonly float $newBaseSalaryAmount,
        public readonly string $newSalaryCurrencyCode,
        public readonly string $newPayFrequency,
        public readonly ?array $newOtherComponentsJson,
    ) {
    }
}
