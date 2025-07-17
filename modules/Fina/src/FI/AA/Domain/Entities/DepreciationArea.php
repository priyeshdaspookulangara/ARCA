<?php

namespace Modules\Fina\FI\AA\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class DepreciationArea extends Model
{
    protected $table = 'fina_aa_depreciation_areas';

    protected $fillable = [
        'code',
        'name',
        'posts_to_gl',
        'depreciation_method_key',
    ];
}
