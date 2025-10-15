<?php

namespace Modules\MM\InventoryManagement\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\MM\MaterialMaster\Domain\Models\Material;

class GoodsIssueItem extends Model
{
    use HasFactory;

    protected $table = 'mm_goods_issue_items';

    protected $fillable = [
        'goods_issue_id',
        'material_id',
        'quantity_issued',
    ];

    public function goodsIssue()
    {
        return $this->belongsTo(GoodsIssue::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}