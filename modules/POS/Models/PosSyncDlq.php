<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosSyncDlq extends Model
{
    use HasFactory;

    protected $table = 'pos_sync_dlq';

    protected $fillable = [
        'event_id',
        'original_payload',
        'error_json',
        'resolution_status',
        'resolved_by',
    ];

    protected $casts = [
        'original_payload' => 'array',
        'error_json' => 'array',
    ];
}