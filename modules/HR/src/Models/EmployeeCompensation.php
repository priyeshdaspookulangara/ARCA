<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCompensation extends Model
{
    protected $table = 'hr_employee_compensation';

    protected $fillable = [
        'employee_id',
        'valid_from',
        'valid_to',
        'action_request_id_triggered_by',
        'base_salary_amount',
        'salary_currency_code',
        'pay_frequency',
        'other_components_json',
        'reason_for_change_code',
    ];

    protected $casts = [
        'other_components_json' => 'array',
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
