<?php

namespace Modules\HR\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// If LeaveRequests are defined, you might add a relationship here
// use Modules\HR\TimeManagement\Domain\Entities\LeaveRequest;

class LeaveType extends Model
{
    use SoftDeletes;
    use HasFactory; // Assuming a factory will be created

    protected $table = 'hr_leave_types';

    protected $fillable = [
        'name',
        'description',
        'is_paid',
        'default_entitlement_days',
        'is_active',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'default_entitlement_days' => 'integer',
    ];

    /**
     * Get all leave requests of this type.
     * (Uncomment and adjust if LeaveRequest model is created and relationship is needed)
     */
    // public function leaveRequests()
    // {
    //     return $this->hasMany(LeaveRequest::class, 'hr_leave_type_id');
    // }

    /**
     * Scope a query to only include active leave types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
