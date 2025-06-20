<?php
namespace Modules\HR\PersonnelAdmin\Application\DTO\DataChange;
class ChangeSalaryRequestDto { public function __construct(public readonly string \$employeeId, public readonly string \$effectiveDate) {} }
