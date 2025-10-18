<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosSyncEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'event_id',
        'idempotency_key',
        'source',
        'type',
        'raw_payload',
        'canonical_payload',
        'status',
        'attempts',
        'first_received_at',
        'last_attempted_at',
        'processed_at',
        'error',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'canonical_payload' => 'array',
        'first_received_at' => 'datetime',
        'last_attempted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function batch()
    {
        return $this->belongsTo(PosSyncBatch::class, 'batch_id');
    }
}