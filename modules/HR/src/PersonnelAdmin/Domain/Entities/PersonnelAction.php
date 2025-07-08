<?php

namespace Modules\HR\PersonnelAdmin\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
// Potentially link to a User model if created_by/approved_by are actual users
// use App\Models\User;

class PersonnelAction extends Model
{
    use SoftDeletes;
    use HasFactory; // Assuming a factory will be created

    protected $table = 'hr_personnel_actions';

    protected $fillable = [
        'hr_employee_id',
        'action_type',
        'effective_date',
        'reason',
        'details_json',
        'status',
        'created_by_user_id',
        'approved_by_user_id',
        'executed_at',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'details_json' => 'array', // Automatically cast JSON to array and vice versa
        'executed_at' => 'datetime',
    ];

    /**
     * Get the employee associated with this personnel action.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'hr_employee_id');
    }

    /**
     * Get the user who created this action.
     * Placeholder: Link to a central User model if it exists.
     */
    // public function createdBy()
    // {
    //     return $this->belongsTo(User::class, 'created_by_user_id');
    // }

    /**
     * Get the user who approved this action.
     * Placeholder: Link to a central User model if it exists.
     */
    // public function approvedBy()
    // {
    //     return $this->belongsTo(User::class, 'approved_by_user_id');
    // }


    // Define constants for action types if desired for easier reference and consistency
    public const ACTION_TYPE_HIRE = 'hire';
    public const ACTION_TYPE_PROMOTION = 'promotion';
    public const ACTION_TYPE_TRANSFER = 'transfer';
    public const ACTION_TYPE_TERMINATION = 'termination';
    public const ACTION_TYPE_CONTRACT_UPDATE = 'contract_update';
    public const ACTION_TYPE_SALARY_CHANGE = 'salary_change';
    // ... other action types

    // Define constants for status types
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_EXECUTED = 'executed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

}
