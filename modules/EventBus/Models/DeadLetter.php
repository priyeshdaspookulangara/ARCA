<?php

namespace Modules\EventBus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeadLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'reason',
        'archived_at',
    ];

    protected $table = 'dead_letter';
}
