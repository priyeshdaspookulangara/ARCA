<?php

namespace Modules\Fina\CO\PC\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCostItem extends Model
{
    use HasFactory;

    protected $table = 'fina_co_pc_product_cost_items';

    protected $fillable = [
        'product_cost_header_id',
        'cost_element_id',
        'activity_type_id',
        'quantity',
        'rate',
        'cost',
    ];

    public function header()
    {
        return $this->belongsTo(ProductCostHeader::class, 'product_cost_header_id');
    }

    public function costElement()
    {
        return $this->belongsTo(CostElement::class, 'cost_element_id');
    }

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }
}