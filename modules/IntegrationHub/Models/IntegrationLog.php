<?php

namespace Modules\IntegrationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'direction',
        'endpoint',
        'status_code',
        'payload',
        'response',
        'timestamp',
    ];
}
