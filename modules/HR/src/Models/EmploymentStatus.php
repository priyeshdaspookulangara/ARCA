<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentStatus extends Model
{
    protected $table = 'hr_employment_statuses';

    protected $fillable = [
        'status_code',
        'description',
    ];
}
