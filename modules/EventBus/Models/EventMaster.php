<?php

namespace Modules\EventBus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'type',
        'source',
        'payload_json',
    ];

    protected $table = 'event_master';
    protected $primaryKey = 'event_id';
    public $incrementing = false;
    protected $keyType = 'string';
}
