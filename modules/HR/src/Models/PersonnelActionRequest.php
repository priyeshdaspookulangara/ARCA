<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class PersonnelActionRequest extends Model
{
    protected $table = 'hr_personnel_action_requests';

    protected $fillable = [
        'request_number',
        'employee_id',
        'action_type_id',
        'requested_effective_date',
        'reason_for_action_text',
        'status',
        'initiator_user_id',
        'submission_datetime',
        'last_approval_datetime',
        'implemented_datetime',
        'workflow_instance_id',
        'current_approver_user_id',
        'proposed_data_snapshot_json',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'proposed_data_snapshot_json' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function actionType()
    {
        return $this->belongsTo(PersonnelActionType::class);
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_user_id');
    }

    public function currentApprover()
    {
        return $this->belongsTo(User::class, 'current_approver_user_id');
    }
}
