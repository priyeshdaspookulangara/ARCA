<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAddress extends Model
{
    protected $table = 'hr_employee_addresses';

    protected $fillable = [
        'employee_id',
        'address_type',
        'valid_from',
        'valid_to',
        'action_request_id_triggered_by',
        'street',
        'city',
        'postal_code',
        'state_or_province',
        'country_code',
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
