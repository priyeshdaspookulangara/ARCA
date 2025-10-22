<?php

namespace Modules\IntegrationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_name',
        'frequency',
        'last_run',
        'next_run',
    ];
}
