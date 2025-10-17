<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POSShift extends Model
{
    use HasFactory;

    protected $table = 'pos_shifts';

    protected $fillable = [
        'terminal_id',
        'cashier_id',
        'start_time',
        'end_time',
        'starting_cash',
        'ending_cash',
    ];

    public function terminal()
    {
        return $this->belongsTo(POSTerminal::class);
    }
}