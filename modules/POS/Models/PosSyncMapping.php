<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosSyncMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'mapping_key',
        'source_type',
        'target_type',
        'config_json',
    ];

    protected $casts = [
        'config_json' => 'array',
    ];
}