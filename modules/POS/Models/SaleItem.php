<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $table = 'pos_sale_items';

    protected $fillable = [
        'sale_id',
        'material_id',
        'quantity',
        'price',
        'tax',
        'discount',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}