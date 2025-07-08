<?php

namespace Modules\HR\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee; // Employee from PersonnelAdmin
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;

class LeaveRequest extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'hr_leave_requests';

    protected $fillable = [
        'hr_employee_id',
        'hr_leave_type_id',
        'start_date',
        'end_date',
        'duration_days',
        'reason',
        'status',
        'approver_user_id',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'cancelled_at',
        'cancelled_by_role',
        'employee_remarks',
        'approver_remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_days' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the employee who requested the leave.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'hr_employee_id');
    }

    /**
     * Get the type of leave requested.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'hr_leave_type_id');
    }

    /**
     * Get the user who approved/rejected this request.
     * Placeholder: Link to a central User model if it exists.
     */
    // public function approver()
    // {
    //     return $this->belongsTo(User::class, 'approver_user_id');
    // }

    // Define constants for status types
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED_BY_EMPLOYEE = 'cancelled_by_employee';
    public const STATUS_CANCELLED_BY_ADMIN = 'cancelled_by_admin'; // Or manager

    // Define constants for cancelled_by_role
    public const CANCELLED_BY_EMPLOYEE_ROLE = 'employee';
    public const CANCELLED_BY_ADMIN_ROLE = 'admin';
    public const CANCELLED_BY_MANAGER_ROLE = 'manager';


    /**
     * Scope a query to only include pending leave requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved leave requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Calculate duration in days, potentially excluding weekends/holidays.
     * Basic version: simple difference.
     * Advanced version would need business days calculation.
     */
    public static function calculateDuration(string $startDate, string $endDate, bool $excludeWeekends = false): float
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        if (!$excludeWeekends) {
            // Includes end date, so add 1 day to the diff
            return $end->diff($start)->days + 1;
        }

        $days = 0;
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start, $interval, (clone $end)->modify('+1 day')); // Include end date in period

        foreach ($period as $date) {
            $dayOfWeek = $date->format('N'); // 1 (Mon) to 7 (Sun)
            if ($dayOfWeek < 6) { // Monday to Friday
                $days++;
            }
        }
        return (float) $days;
        // Note: For half-days, this logic would need to be more granular or handled separately.
        // This simple version assumes full days. For 0.5 day duration, it should be set manually.
    }
}
