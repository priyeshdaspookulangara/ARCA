<?php

namespace Modules\RTH\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RthEvent extends Model
{
    use HasFactory;

    protected $table = 'rth_events';

    protected $fillable = [
        'event_id',
        'source',
        'type',
        'canonical_payload',
        'status',
        'attempts',
        'first_received_at',
        'last_attempted_at',
        'processed_at',
        'idempotency_key',
        'error',
    ];

    protected $casts = [
        'canonical_payload' => 'array',
    ];
}