<?php

namespace Modules\HR\PersonnelAdmin\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // If you plan to use factories
use Modules\HR\PersonnelAdmin\Domain\Entities\Position; // For positions relationship

class Job extends Model
{
    use SoftDeletes;
    // use HasFactory; // Uncomment if you create a factory for Job

    protected $table = 'hr_jobs';

    protected $fillable = [
        'job_title',
        'job_description',
        'job_code',
        'min_salary',
        'max_salary',
    ];

    /**
     * Get all positions associated with this job.
     */
    public function positions()
    {
        return $this->hasMany(Position::class, 'hr_job_id');
    }
}
