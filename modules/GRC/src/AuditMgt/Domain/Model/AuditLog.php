<?php

namespace Modules\GRC\AuditMgt\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'grc_audit_logs';

    protected $fillable = [
        'module',
        'entity_type',
        'entity_id',
        'action',
        'payload_json',
        'user_id',
        'hash',
        'previous_hash',
    ];

    protected $casts = [
        'payload_json' => 'array',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\GRC\Database\factories\AuditLogFactory::new();
    }
}