<?php

namespace Modules\MM\MaterialMaster\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\MM\Procurement\Domain\Models\Supplier;

class Material extends Model
{
    use HasFactory;

    protected $table = 'mm_materials';

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'uom',
        'valuation_method',
        'reorder_level',
        'min_quantity',
        'max_quantity',
        'default_supplier_id',
        'purchase_price',
        'selling_price',
        'inventory_account',
        'cogs_account',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'default_supplier_id');
    }
}