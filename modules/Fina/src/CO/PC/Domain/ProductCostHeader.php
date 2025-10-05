<?php

namespace Modules\Fina\CO\PC\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCostHeader extends Model
{
    use HasFactory;

    protected $table = 'fina_co_pc_product_cost_headers';

    protected $fillable = [
        'product_id',
        'costing_variant',
        'costing_date',
        'total_cost',
    ];

    public function items()
    {
        return $this->hasMany(ProductCostItem::class, 'product_cost_header_id');
    }
}