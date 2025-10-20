<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OfflineTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'timestamp',
        'payload_json',
        'status',
        'sync_attempts',
    ];

    public function setPayloadJsonAttribute($value)
    {
        $this->attributes['payload_json'] = Crypt::encryptString($value);
    }

    public function getPayloadJsonAttribute($value)
    {
        return Crypt::decryptString($value);
    }
}
