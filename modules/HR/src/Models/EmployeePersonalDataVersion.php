<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePersonalDataVersion extends Model
{
    protected $table = 'hr_employee_personal_data_versions';

    protected $fillable = [
        'employee_id',
        'valid_from',
        'valid_to',
        'action_request_id_triggered_by',
        'last_name',
        'first_name',
        'marital_status_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'bank_account_details_json_encrypted',
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
