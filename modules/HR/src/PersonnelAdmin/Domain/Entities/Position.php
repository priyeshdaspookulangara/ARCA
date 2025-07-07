<?php

namespace Modules\HR\PersonnelAdmin\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // If you plan to use factories
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;

class Position extends Model
{
    use SoftDeletes;
    // use HasFactory; // Uncomment if you create a factory for Position

    protected $table = 'hr_positions';

    protected $fillable = [
        'position_title',
        'hr_job_id',
        'hr_department_id',
        'description',
        'reports_to_position_id',
        'is_vacant',
        'effective_date_start',
        'effective_date_end',
    ];

    protected $casts = [
        'is_vacant' => 'boolean',
        'effective_date_start' => 'date',
        'effective_date_end' => 'date',
    ];

    /**
     * Get the job associated with this position.
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'hr_job_id');
    }

    /**
     * Get the department this position belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'hr_department_id');
    }

    /**
     * Get the position this position reports to.
     */
    public function reportsTo()
    {
        return $this->belongsTo(Position::class, 'reports_to_position_id');
    }

    /**
     * Get the direct reports for this position.
     */
    public function directReports()
    {
        return $this->hasMany(Position::class, 'reports_to_position_id');
    }

    /**
     * Get the employee currently holding this position.
     */
    public function currentEmployee()
    {
        // This assumes 'hr_position_id' is on the hr_employees table
        // and that an employee is marked as not 'active' or has a termination_date if they no longer hold the position.
        // For a truly "current" employee, you might also check employee's status.
        return $this->hasOne(Employee::class, 'hr_position_id')->where(function ($query) {
            $query->where('employment_status', 'active') // Example: only active employees
                  ->orWhereNull('termination_date'); // Or those not yet terminated
        });
    }

    /**
     * Check if this position is an ancestor of another position in the reporting hierarchy.
     */
    public function isAncestorOf(Position $otherPosition): bool
    {
        $parent = $otherPosition->reportsTo;
        while ($parent) {
            if ($parent->id === $this->id) {
                return true;
            }
            // Safeguard against misconfigured data causing infinite loop
            if ($parent->id === $parent->reports_to_position_id) {
                return false;
            }
            $parent = $parent->reportsTo;
        }
        return false;
    }

    /**
     * Get all employees who have historically been assigned to this position.
     * This would typically require an intermediary table (e.g., employee_position_history)
     * that logs assignments with start and end dates.
     * For now, this is a placeholder if such detailed tracking isn't implemented.
     */
    // public function employeeAssignmentHistory() { ... }
}
