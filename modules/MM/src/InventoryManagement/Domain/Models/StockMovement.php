<?php

namespace Modules\MM\InventoryManagement\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\MM\MaterialMaster\Domain\Models\Material;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'mm_stock_movements';

    protected $fillable = [
        'material_id',
        'movement_type',
        'quantity',
        'location_id',
        'reference_id',
        'reference_type',
        'cost',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}