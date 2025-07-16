<?php

namespace Modules\HR\Payroll\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeductionType extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'hr_deduction_types';

    protected $fillable = [
        'name',
        'is_pre_tax',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_pre_tax' => 'boolean',
        'is_active' => 'boolean',
    ];
}
