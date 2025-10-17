<?php

namespace Modules\CRM\CCC\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommunicationChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // e.g., 'email', 'chat', 'phone'
        'is_active',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\CommunicationChannelFactory::new();
    }
}