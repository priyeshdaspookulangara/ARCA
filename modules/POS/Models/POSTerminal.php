<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POSTerminal extends Model
{
    use HasFactory;

    protected $table = 'pos_terminals';

    protected $fillable = [
        'device_id',
        'branch',
    ];
}