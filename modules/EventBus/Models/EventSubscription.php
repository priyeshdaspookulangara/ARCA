<?php

namespace Modules\EventBus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic',
        'subscriber_module',
        'endpoint_url',
    ];

    protected $table = 'event_subscriptions';
}
