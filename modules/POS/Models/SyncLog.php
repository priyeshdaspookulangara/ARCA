<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'count',
        'status',
        'last_synced',
    ];

    protected $table = 'sync_log';
    protected $primaryKey = 'batch_id';
    public $incrementing = false;
    protected $keyType = 'string';
}
