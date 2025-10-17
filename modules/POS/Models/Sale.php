<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'pos_sales';

    protected $fillable = [
        'shift_id',
        'total_amount',
        'tax_amount',
        'discount_amount',
    ];

    public function shift()
    {
        return $this->belongsTo(POSShift::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}