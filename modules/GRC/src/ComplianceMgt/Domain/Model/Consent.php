<?php

namespace Modules\GRC\ComplianceMgt\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consent extends Model
{
    use HasFactory;

    protected $table = 'grc_consents';

    protected $fillable = [
        'customer_id',
        'purpose',
        'granted_at',
        'expires_at',
        'origin',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\GRC\Database\factories\ConsentFactory::new();
    }
}