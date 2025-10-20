<?php

namespace Modules\AuthMgt\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'event_type',
        'user_id',
        'auditable_id',
        'auditable_type',
        'details',
        'ip_address',
    ];

    /**
     * Get the parent auditable model (user, role, etc.).
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}