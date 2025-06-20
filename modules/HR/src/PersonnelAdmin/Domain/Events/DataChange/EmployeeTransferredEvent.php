<?php
namespace Modules\HR\PersonnelAdmin\Domain\Events\DataChange;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class EmployeeTransferredEvent { use Dispatchable, SerializesModels; public function __construct(public readonly string \$employeeId, public readonly string \$newOrgUnitId, public readonly string \$effectiveDate) {} }
