<?php

namespace Modules\MM\InventoryManagement\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsIssue extends Model
{
    use HasFactory;

    protected $table = 'mm_goods_issues';

    protected $fillable = [
        'issue_type',
        'reference_id',
        'issue_date',
        'status',
        'notes',
    ];

    public function items()
    {
        return $this->hasMany(GoodsIssueItem::class);
    }
}