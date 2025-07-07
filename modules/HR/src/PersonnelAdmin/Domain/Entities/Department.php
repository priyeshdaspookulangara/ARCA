<?php

namespace Modules\HR\PersonnelAdmin\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // If you plan to use factories
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee; // For manager relationship
use Modules\HR\PersonnelAdmin\Domain\Entities\Position; // For positions relationship

class Department extends Model
{
    use SoftDeletes;
    // use HasFactory; // Uncomment if you create a factory for Department

    protected $table = 'hr_departments';

    protected $fillable = [
        'name',
        'description',
        'parent_department_id',
        'manager_id',
    ];

    /**
     * Get the parent department.
     */
    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'parent_department_id');
    }

    /**
     * Get the child departments.
     */
    public function childDepartments()
    {
        return $this->hasMany(Department::class, 'parent_department_id');
    }

    /**
     * Get the manager of the department.
     * Assuming 'manager_id' on 'hr_departments' table links to an 'id' on 'hr_employees' table.
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Get all positions within this department.
     */
    public function positions()
    {
        return $this->hasMany(Position::class, 'hr_department_id');
    }

    /**
     * Get all employees directly associated with this department.
     * This assumes a direct 'hr_department_id' on the 'hr_employees' table.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'hr_department_id');
    }

    /**
     * Check if this department is an ancestor of another department.
     * Used to prevent circular dependencies.
     */
    public function isAncestorOf(Department $otherDepartment): bool
    {
        $parent = $otherDepartment->parentDepartment;
        while ($parent) {
            if ($parent->id === $this->id) {
                return true;
            }
            // Check for infinite loop potential if data is corrupted, though unlikely with proper constraints
            if ($parent->id === $parent->parent_department_id) {
                // This case should ideally not happen with proper DB constraints or validation
                // but as a safeguard for the loop:
                return false; // or throw an exception
            }
            $parent = $parent->parentDepartment;
        }
        return false;
    }
}
