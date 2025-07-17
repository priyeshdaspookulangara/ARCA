<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeJobAssignment extends Model
{
    protected $table = 'hr_employee_job_assignments';

    protected $fillable = [
        'employee_id',
        'valid_from',
        'valid_to',
        'action_request_id_triggered_by',
        'position_id',
        'job_title_id',
        'department_id',
        'cost_center_id',
        'company_code_id',
        'personnel_area_id',
        'personnel_sub_area_id',
        'employee_group_id',
        'employee_sub_group_id',
        'manager_core_user_id',
        'employment_status_id',
        'reason_for_change_code',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function actionRequest()
    {
        return $this->belongsTo(PersonnelActionRequest::class, 'action_request_id_triggered_by');
    }

    public function employmentStatus()
    {
        return $this->belongsTo(EmploymentStatus::class);
    }
}
