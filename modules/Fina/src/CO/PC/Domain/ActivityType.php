<?php

namespace Modules\Fina\CO\PC\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityType extends Model
{
    use HasFactory;

    protected $table = 'fina_co_pc_activity_types';

    protected $fillable = [
        'name',
        'unit',
        'description',
    ];
}