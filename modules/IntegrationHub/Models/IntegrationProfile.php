<?php

namespace Modules\IntegrationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'system_name',
        'type',
        'auth_mode',
        'config',
        'status',
    ];

    protected $casts = [
        'config' => 'array',
    ];
}
