<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class PersonnelActionType extends Model
{
    protected $table = 'hr_personnel_action_types';

    protected $fillable = [
        'action_code',
        'description',
        'default_workflow_definition_key',
        'is_ess_allowed',
        'is_mss_allowed',
    ];
}
