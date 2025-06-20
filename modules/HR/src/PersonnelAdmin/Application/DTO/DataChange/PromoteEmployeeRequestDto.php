<?php
namespace Modules\HR\PersonnelAdmin\Application\DTO\DataChange;
// Basic DTO, would include new_position_id, new_salary_details, effective_date etc.
class PromoteEmployeeRequestDto { public function __construct(public readonly string \$employeeId, public readonly string \$effectiveDate) {} }
