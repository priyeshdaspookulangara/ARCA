<?php

namespace Modules\HR\PersonnelAdmin\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // If you plan to use factories
use Modules\HR\PersonnelAdmin\Domain\Entities\Position;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;

class Employee extends Model
{
    use SoftDeletes;
    // use HasFactory; // Uncomment if you create a factory for Employee

    protected $table = 'hr_employees';

    protected $fillable = [
        'employee_id_number',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'nationality',
        'marital_status',
        'personal_email',
        'work_email',
        'phone_mobile',
        'phone_work',
        'address_line_1',
        'address_line_2',
        'city',
        'state_province',
        'postal_code',
        'country',
        'hr_position_id',
        'hr_department_id', // Can be derived via position, but often kept for direct access
        'hire_date',
        'termination_date',
        'employment_status',
        'employment_type',
        // 'user_id', // If linking to a central user table
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
    ];

    /**
     * Get the position of the employee.
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'hr_position_id');
    }

    /**
     * Get the department of the employee.
     * This provides a direct link if 'hr_department_id' is stored on the employees table.
     * Alternatively, it could be accessed via the position: $this->position->department.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'hr_department_id');
    }

    /**
     * Get the full name of the employee.
     */
    public function getFullNameAttribute(): string
    {
        $parts = [$this->first_name, $this->middle_name, $this->last_name];
        return implode(' ', array_filter($parts));
    }

    /**
     * Scope a query to only include active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active')->whereNull('termination_date');
    }

    // Relationship to a central User model (if it exists and is linked)
    // public function user()
    // {
    //    return $this->belongsTo(\App\Models\User::class, 'user_id'); // Adjust namespace as needed
    // }

    /**
     * Get the manager of this employee.
     * This would typically be derived from the employee's position's reports_to_position_id,
     * and then finding the employee in that 'reports_to' position.
     */
    public function getManagerAttribute()
    {
        if ($this->position && $this->position->reportsTo && $this->position->reportsTo->currentEmployee) {
            return $this->position->reportsTo->currentEmployee;
        }
        return null;
    }

    /**
     * Get direct reports if this employee is a manager.
     * This implies finding all positions that report to this employee's position,
     * and then getting the employees in those positions.
     */
    public function getDirectReportsAttribute()
    {
        if (!$this->position) {
            return collect(); // Return an empty collection
        }

        return Employee::whereHas('position', function ($query) {
            $query->where('reports_to_position_id', $this->hr_position_id);
        })->get();
    }

    /**
     * Get all personnel actions for the employee.
     */
    public function personnelActions()
    {
        return $this->hasMany(PersonnelAction::class, 'hr_employee_id')->orderBy('effective_date', 'desc');
    }

    /**
     * Get all contracts for the employee.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'hr_employee_id')->orderBy('start_date', 'desc');
    }

    /**
     * Get the current active contract for the employee.
     */
    public function currentContract()
    {
        return $this->hasOne(Contract::class, 'hr_employee_id')
                    ->where('status', Contract::STATUS_ACTIVE)
                    ->where('start_date', '<=', now())
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>=', now());
                    })
                    ->latest('start_date'); // Get the most recent active contract
    }
}
