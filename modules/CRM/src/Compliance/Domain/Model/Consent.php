<?php

namespace Modules\CRM\Compliance\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consent extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'channel',
        'granted',
        'source',
    ];

    public function customer()
    {
        return $this->belongsTo(\Modules\CRM\CustomerMaster\Domain\Model\Customer::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\ConsentFactory::new();
    }
}