<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class PersonnelActionApprovalHistory extends Model
{
    protected $table = 'hr_personnel_action_approval_history';

    protected $fillable = [
        'action_request_id',
        'approval_step_name',
        'approver_user_id',
        'decision',
        'decision_datetime',
        'comments',
    ];

    public function actionRequest()
    {
        return $this->belongsTo(PersonnelActionRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
