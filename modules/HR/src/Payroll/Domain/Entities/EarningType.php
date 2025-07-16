<?php

namespace Modules\HR\Payroll\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EarningType extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'hr_earning_types';

    protected $fillable = [
        'name',
        'is_taxable',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
    ];
}
