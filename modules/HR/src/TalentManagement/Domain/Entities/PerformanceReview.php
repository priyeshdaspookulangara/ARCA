<?php

namespace Modules\HR\TalentManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;

class PerformanceReview extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'hr_performance_reviews';

    protected $fillable = [
        'hr_employee_id',
        'reviewer_id',
        'review_period_start_date',
        'review_period_end_date',
        'overall_rating',
        'strengths',
        'areas_for_improvement',
        'employee_comments',
        'manager_comments',
        'status',
        'finalized_at',
    ];

    protected $casts = [
        'review_period_start_date' => 'date',
        'review_period_end_date' => 'date',
        'finalized_at' => 'datetime',
        'overall_rating' => 'integer',
    ];

    /**
     * Get the employee being reviewed.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'hr_employee_id');
    }

    /**
     * Get the reviewer (manager).
     */
    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    // Define constants for status types
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_EMPLOYEE_REVIEW = 'pending_employee_review';
    public const STATUS_PENDING_MANAGER_REVIEW = 'pending_manager_review';
    public const STATUS_FINALIZED = 'finalized';
}
