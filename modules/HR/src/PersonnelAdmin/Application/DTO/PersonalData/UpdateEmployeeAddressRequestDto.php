<?php

namespace Modules\HR\PersonnelAdmin\Application\DTO\PersonalData;

class UpdateEmployeeAddressRequestDto
{
    public function __construct(
        public readonly int $employeeId,
        public readonly int $actionRequestId,
        public readonly string $effectiveDate,
        public readonly string $addressType,
        public readonly string $street,
        public readonly string $city,
        public readonly string $postalCode,
        public readonly string $stateOrProvince,
        public readonly string $countryCode,
    ) {
    }
}
