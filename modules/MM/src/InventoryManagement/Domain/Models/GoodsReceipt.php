<?php

namespace Modules\MM\InventoryManagement\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\MM\Procurement\Domain\Models\PurchaseOrder;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $table = 'mm_goods_receipts';

    protected $fillable = [
        'purchase_order_id',
        'receipt_date',
        'status',
        'notes',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }
}