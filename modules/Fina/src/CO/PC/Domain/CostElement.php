<?php

namespace Modules\Fina\CO\PC\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CostElement extends Model
{
    use HasFactory;

    protected $table = 'fina_co_pc_cost_elements';

    protected $fillable = [
        'name',
        'type',
        'description',
    ];
}