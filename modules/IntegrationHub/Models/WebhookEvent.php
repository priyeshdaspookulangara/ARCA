<?php

namespace Modules\IntegrationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'payload',
        'status',
        'processed_at',
    ];
}
