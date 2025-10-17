<?php

namespace Modules\CRM\CCC\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'communication_channel_id',
        'direction', // 'inbound' or 'outbound'
        'content',
        'sent_at',
    ];

    public function customer()
    {
        return $this->belongsTo(\Modules\CRM\CustomerMaster\Domain\Model\Customer::class);
    }

    public function communicationChannel()
    {
        return $this->belongsTo(CommunicationChannel::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\MessageFactory::new();
    }
}