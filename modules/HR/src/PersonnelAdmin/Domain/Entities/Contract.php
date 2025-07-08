<?php

namespace Modules\HR\PersonnelAdmin\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;

class Contract extends Model
{
    use SoftDeletes;
    use HasFactory; // Assuming a factory will be created

    protected $table = 'hr_contracts';

    protected $fillable = [
        'hr_employee_id',
        'contract_type',
        'start_date',
        'end_date',
        'job_title_snapshot',
        'department_snapshot',
        'salary_amount',
        'salary_currency',
        'salary_frequency',
        'working_hours_per_week',
        'probation_period_months',
        'notice_period_days',
        'contract_document_path',
        'status',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'salary_amount' => 'decimal:2',
        'working_hours_per_week' => 'decimal:2',
    ];

    /**
     * Get the employee associated with this contract.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'hr_employee_id');
    }

    // Define constants for contract types if desired
    public const TYPE_PERMANENT = 'permanent';
    public const TYPE_FIXED_TERM = 'fixed-term';
    public const TYPE_INTERNSHIP = 'internship';
    public const TYPE_PART_TIME = 'part-time';
    // ... other types

    // Define constants for status types
    public const STATUS_PENDING_SIGNATURE = 'pending_signature';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_TERMINATED_EARLY = 'terminated_early';
    public const STATUS_SUPERSEDED = 'superseded'; // When a new contract replaces this one

    // Define constants for salary frequency
    public const FREQUENCY_HOURLY = 'hourly';
    public const FREQUENCY_DAILY = 'daily';
    public const FREQUENCY_WEEKLY = 'weekly';
    public const FREQUENCY_MONTHLY = 'monthly';
    public const FREQUENCY_ANNUAL = 'annual';

    /**
     * Scope a query to only include active contracts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where('start_date', '<=', now())
                     ->where(function ($q) {
                         $q->whereNull('end_date')
                           ->orWhere('end_date', '>=', now());
                     });
    }
}
