<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeWorkSchedule extends Model
{
    protected $table = 'hr_employee_work_schedules';

    protected $fillable = [
        'employee_id',
        'valid_from',
        'valid_to',
        'action_request_id_triggered_by',
        'employment_type_id',
        'work_schedule_rule_id',
        'weekly_hours',
        'fte_percentage',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function actionRequest()
    {
        return $this->belongsTo(PersonnelActionRequest::class, 'action_request_id_triggered_by');
    }
}
