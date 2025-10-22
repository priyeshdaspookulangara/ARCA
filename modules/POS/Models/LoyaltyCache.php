<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyCache extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'tier',
        'points_balance',
        'last_updated',
    ];

    protected $table = 'loyalty_cache';
    protected $primaryKey = 'program_id';
    public $incrementing = false;
    protected $keyType = 'string';
}
