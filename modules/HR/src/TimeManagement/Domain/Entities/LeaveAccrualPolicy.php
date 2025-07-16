<?php

namespace Modules\HR\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;

class LeaveAccrualPolicy extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'hr_leave_accrual_policies';

    protected $fillable = [
        'name',
        'hr_leave_type_id',
        'accrual_frequency',
        'accrual_rate_days',
        'max_carry_over_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'accrual_rate_days' => 'decimal:2',
        'max_carry_over_days' => 'decimal:2',
    ];

    /**
     * Get the leave type this policy applies to.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'hr_leave_type_id');
    }

    // Constants for accrual frequency
    public const FREQUENCY_ANNUALLY = 'annually';
    public const FREQUENCY_MONTHLY = 'monthly';
    public const FREQUENCY_QUARTERLY = 'quarterly';
    public const FREQUENCY_BI_WEEKLY = 'bi-weekly';

    /**
     * Scope a query to only include active policies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
