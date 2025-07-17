<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLongTermLeave extends Model
{
    protected $table = 'hr_employee_long_term_leaves';

    protected $fillable = [
        'employee_id',
        'action_request_id_start',
        'action_request_id_end',
        'leave_type_id',
        'planned_start_date',
        'actual_start_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'notes',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function startActionRequest()
    {
        return $this->belongsTo(PersonnelActionRequest::class, 'action_request_id_start');
    }

    public function endActionRequest()
    {
        return $this->belongsTo(PersonnelActionRequest::class, 'action_request_id_end');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
