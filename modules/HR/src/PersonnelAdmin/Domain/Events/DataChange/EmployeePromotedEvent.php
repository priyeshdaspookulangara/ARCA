<?php
namespace Modules\HR\PersonnelAdmin\Domain\Events\DataChange;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class EmployeePromotedEvent { use Dispatchable, SerializesModels; public function __construct(public readonly string \$employeeId, public readonly string \$newPositionId, public readonly string \$effectiveDate) {} }
