<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'pos_payments';

    protected $fillable = [
        'sale_id',
        'payment_mode',
        'amount',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}