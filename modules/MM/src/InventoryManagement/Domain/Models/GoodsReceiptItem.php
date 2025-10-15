<?php

namespace Modules\MM\InventoryManagement\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\MM\MaterialMaster\Domain\Models\Material;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    protected $table = 'mm_goods_receipt_items';

    protected $fillable = [
        'goods_receipt_id',
        'material_id',
        'quantity_received',
    ];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}