<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosSyncBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'source',
        'events_count',
        'status',
        'processed_at',
        'last_error',
    ];

    public function events()
    {
        return $this->hasMany(PosSyncEvent::class, 'batch_id');
    }
}