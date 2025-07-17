<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'hr_leave_types';

    protected $fillable = [
        'leave_type_code',
        'description',
        'affects_payroll',
        'is_paid_leave',
    ];
}
