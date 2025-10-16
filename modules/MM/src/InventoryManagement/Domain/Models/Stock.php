<?php

namespace Modules\MM\InventoryManagement\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\MM\MaterialMaster\Domain\Models\Material;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'mm_stocks';

    protected $fillable = [
        'material_id',
        'location_id',
        'quantity',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}