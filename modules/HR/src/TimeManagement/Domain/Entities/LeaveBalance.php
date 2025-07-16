<?php

namespace Modules\HR\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;

class LeaveBalance extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'hr_leave_balances';

    protected $fillable = [
        'hr_employee_id',
        'hr_leave_type_id',
        'fiscal_year',
        'entitlement_days',
        'taken_days',
        // 'balance_days' is a calculated column in the DB, so not fillable
        'notes',
    ];

    protected $casts = [
        'entitlement_days' => 'decimal:2',
        'taken_days' => 'decimal:2',
        'balance_days' => 'decimal:2', // Even though calculated, casting helps when retrieving
    ];

    /**
     * Get the employee this balance belongs to.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'hr_employee_id');
    }

    /**
     * Get the leave type this balance is for.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'hr_leave_type_id');
    }
}
