<?php

namespace Modules\MM\Procurement\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\MM\MaterialMaster\Domain\Models\Material;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'mm_purchase_order_items';

    protected $fillable = [
        'purchase_order_id',
        'material_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}