<?php

namespace Modules\EventBus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'status',
        'attempts',
        'last_attempt',
        'error_log',
    ];

    protected $table = 'event_audit';
}
