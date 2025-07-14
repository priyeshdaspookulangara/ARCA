<?php

namespace Modules\HR\TalentManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;

class JobApplication extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'hr_job_applications';

    protected $fillable = [
        'hr_job_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'resume_path',
        'cover_letter',
        'status',
        'applied_date',
        'notes',
    ];

    protected $casts = [
        'applied_date' => 'date',
    ];

    /**
     * Get the job for which the application was submitted.
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'hr_job_id');
    }

    // Add relationship to Job model
    // In Modules\HR\PersonnelAdmin\Domain\Entities\Job.php:
    /*
    public function applications()
    {
        return $this->hasMany(\Modules\HR\TalentManagement\Domain\Entities\JobApplication::class, 'hr_job_id');
    }
    */


    // Define constants for status types
    public const STATUS_APPLIED = 'applied';
    public const STATUS_SCREENING = 'screening';
    public const STATUS_INTERVIEWING = 'interviewing';
    public const STATUS_OFFERED = 'offered';
    public const STATUS_HIRED = 'hired';
    public const STATUS_REJECTED = 'rejected';

    /**
     * Get the full name of the applicant.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
